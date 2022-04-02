<?php
/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2017-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\System;

use Automad\Core\FileSystem;
use Automad\Core\Parse;
use Automad\Core\Str;
use Automad\UI\Components\Alert\Alert;
use Automad\UI\Components\Alert\Danger;
use Automad\UI\Components\Alert\Success;
use Automad\UI\Response;
use Automad\UI\Utils\Prefix;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Update class handles the process of updating Automad using the dashboard.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2017-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Update {
	/**
	 * The update timestamp.
	 */
	private static $timestamp = null;

	/**
	 * Download version file and extract version number.
	 *
	 * @return string Version number or false on error.
	 */
	public static function getVersion() {
		$version = false;

		$options = array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_URL => AM_UPDATE_REPO_RAW_URL . '/' . AM_UPDATE_BRANCH . AM_UPDATE_REPO_VERSION_FILE
		);

		$curl = curl_init();
		curl_setopt_array($curl, $options);
		$output = curl_exec($curl);

		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200 && !curl_errno($curl)) {
			$version = self::extractVersion($output);
		}

		curl_close($curl);

		return $version;
	}

	/**
	 * Run the actual update.
	 *
	 * @return Response the response object
	 */
	public static function run() {
		self::$timestamp = date('Ymd-His');
		self::log('Starting update ' . date('c'));

		$Response = new Response();
		$items = self::items();

		if (!$items) {
			$Response->setHtml(Danger::render(Text::get('error_update_items')));
			$Response->setCli('Invalid list of items to be updated!');

			return $Response;
		}

		if ($phpVersion = self::higherPHPVersionRequired()) {
			$Response->setHtml(Danger::render(Text::get('error_update_php_version') . " $phpVersion+."));
			$Response->setCli("Your PHP version doesn't support the latest Automad version. Please upgrade PHP to version $phpVersion+.");

			return $Response;
		}

		if (!self::permissionsGranted($items)) {
			$Response->setHtml(Danger::render(Text::get('error_update_permission')));
			$Response->setCli('Permission denied!');

			return $Response;
		}

		self::log('Version to be updated: ' . AM_VERSION);
		self::log('Updating items: ' . implode(', ', $items));

		if ($archive = self::getArchive()) {
			if (self::backupCurrent($items)) {
				if (self::unpack($archive, $items)) {
					$success = true;

					// Clear cache.
					if (file_exists(AM_FILE_SITE_MTIME)) {
						unlink(AM_FILE_SITE_MTIME);
					}
				} else {
					$success = false;
				}
			} else {
				$success = false;
			}

			if ($success) {
				$version = '';
				$versionFile = AM_BASE_DIR . '/automad/version.php';

				if (is_readable($versionFile)) {
					$version = self::extractVersion(file_get_contents($versionFile));
				}

				$log = self::log('Successfully updated Automad to version ' . $version);
				$logUrl = str_replace(AM_BASE_DIR, AM_BASE_URL, $log);

				$Response->setHtml(
					Success::render(
						Text::get('sys_update_not_required') . ' ' .
						Text::get('sys_update_current_version') . ' ' .
						$version . '.'
					) .
					'<a href="' . $logUrl . '" target="_blank" class="uk-button">' .
						'<i class="uk-icon-file-text-o"></i>&nbsp;&nbsp;' .
						Text::get('btn_open_log') .
					'</a>'
				);

				$Response->setSuccess(Text::get('success_update'));
				$Response->setCli('Successfully updated to version ' . $version);
			} else {
				$Response->setHtml(Danger::render(Text::get('error_update_failed')));
			}
		} else {
			$Response->setHtml(Danger::render(Text::get('error_update_download')));
		}

		return $Response;
	}

	/**
	 * Test if the server supports all required functions.
	 *
	 * @return bool True on success, false on error
	 */
	public static function supported() {
		return (function_exists('curl_version') && class_exists('ZipArchive'));
	}

	/**
	 * Move currently installed items to /cache/update/backup.
	 *
	 * @param array $items
	 * @return bool True on success, false on error
	 */
	private static function backupCurrent(array $items) {
		self::preloadClasses();

		$backup = AM_BASE_DIR . AM_UPDATE_TEMP . '/backup/' . self::$timestamp;

		FileSystem::makeDir($backup);

		foreach ($items as $item) {
			$itemPath = AM_BASE_DIR . $item;
			$backupPath = $backup . $item;

			// Only try to backup in case item exists.
			if (file_exists($itemPath)) {
				if (is_writable($itemPath) && is_writable(dirname($itemPath))) {
					FileSystem::makeDir(dirname($backupPath));
					$success = rename($itemPath, $backupPath);
					self::log('Backing up ' . Str::stripStart($itemPath, AM_BASE_DIR) . ' to ' . Str::stripStart($backupPath, AM_BASE_DIR));
				} else {
					$success = false;
				}

				if (!$success) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Extract version number form content of version.php.
	 *
	 * @param string $str
	 * @return string The version number
	 */
	private static function extractVersion(string $str) {
		if (preg_match('/\d[^\'"]+/', $str, $matches)) {
			return $matches[0];
		}

		return '';
	}

	/**
	 * Download zip-archive to be installed.
	 *
	 * @return string Path to the downloaded archive or false on error
	 */
	private static function getArchive() {
		$archive = AM_BASE_DIR . AM_UPDATE_TEMP . '/download/' . self::$timestamp . '.zip';

		FileSystem::makeDir(dirname($archive));

		set_time_limit(0);

		$fp = fopen($archive, 'w+');

		$options = array(
			CURLOPT_TIMEOUT => 120,
			CURLOPT_FILE => $fp,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_URL => AM_UPDATE_REPO_DOWNLOAD_URL . '/' . AM_UPDATE_BRANCH . '.zip'
		);

		$curl = curl_init();
		curl_setopt_array($curl, $options);
		curl_exec($curl);

		self::log('Downloading branch "' . AM_UPDATE_BRANCH . '" form ' . AM_UPDATE_REPO_DOWNLOAD_URL);

		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200 || curl_errno($curl)) {
			$archive = false;
			self::log('Download failed!');
		}

		curl_close($curl);
		fclose($fp);

		return $archive;
	}

	/**
	 * Check if the server's PHP version matches the minimum requirements in the remote composer.json file.
	 *
	 * @return string a version number in case PHP is outdated or an empty string
	 */
	private static function higherPHPVersionRequired() {
		$requiredVersion = '';

		$options = array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_URL => AM_UPDATE_REPO_RAW_URL . '/' . AM_UPDATE_BRANCH . '/composer.json'
		);

		$curl = curl_init();
		curl_setopt_array($curl, $options);
		$output = curl_exec($curl);

		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200 && !curl_errno($curl)) {
			try {
				$data = json_decode($output);
				$composerRequiredVersion = preg_replace('/[^\d\.]*/', '', $data->require->php);

				self::log("The required PHP version is $composerRequiredVersion");

				if (version_compare(PHP_VERSION, $composerRequiredVersion, '<')) {
					$requiredVersion = $composerRequiredVersion;
					self::log("The server's PHP version in outdated!");
				}
			} catch (\Exception $e) {
				self::log($e->getMessage());
			}
		}

		curl_close($curl);

		return $requiredVersion;
	}

	/**
	 * Get items to be updated from config.
	 *
	 * @return array The array of items to be updated or false on error
	 */
	private static function items() {
		$items = Parse::csv(AM_UPDATE_ITEMS);

		if (is_array($items)) {
			$items = array_filter($items);

			if (!empty($items)) {
				return $items;
			}
		}

		return false;
	}

	/**
	 * Log events to the update log file.
	 *
	 * @param string $data
	 * @return string The path to the log file
	 */
	private static function log(string $data) {
		$file = AM_BASE_DIR . AM_UPDATE_TEMP . '/' . self::$timestamp . '.log';
		FileSystem::makeDir(dirname($file));
		file_put_contents($file, $data . "\r\n", FILE_APPEND);

		return $file;
	}

	/**
	 * Test if permissions for all items to be updated are granted.
	 *
	 * @param array $items
	 * @return bool True on success, false on error
	 */
	private static function permissionsGranted(array $items) {
		foreach ($items as $item) {
			$item = AM_BASE_DIR . $item;

			if ((file_exists($item) && !is_writable($item)) || !is_writable(dirname($item))) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Preload required classes before removing old installation.
	 */
	private static function preloadClasses() {
		Prefix::tags('');
		Alert::render('');
		Danger::render('');
		Success::render('');
		Text::getObject();
	}

	/**
	 * Unpack all item matching AM_UPDATE_ITEM.
	 *
	 * @param string $archive
	 * @param array $items
	 * @return bool True on success, false on error
	 */
	private static function unpack(string $archive, array $items) {
		$success = true;
		$zip = new \ZipArchive();
		$itemsMatchRegex = '/^[\w\-]+(' . addcslashes(implode('|', $items), '/') . ')/';

		if ($zip->open($archive)) {
			// Iterate over zip entries and unpack item in case
			// the name matches on of the update items.
			for ($i = 0; $i < $zip->numFiles; $i++) {
				$name = $zip->getNameIndex($i);

				if (preg_match($itemsMatchRegex, $name)) {
					$filename = AM_BASE_DIR . preg_replace('/^([\w\-]+)/', '', $name);

					if (FileSystem::write($filename, $zip->getFromName($name)) !== false) {
						self::log('Extracted ' . Str::stripStart($filename, AM_BASE_DIR));
					} else {
						self::log('Error extracting ' . Str::stripStart($filename, AM_BASE_DIR));
						$success = false;
					}
				}
			}

			$zip->close();
		} else {
			$success = false;
		}

		unlink($archive);

		return $success;
	}
}
