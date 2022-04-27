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

use Automad\Admin\UI\Utils\Messenger;
use Automad\Admin\UI\Utils\Text;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\Parse;
use Automad\Core\Str;

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
		$versionFileUrl = AM_UPDATE_REPO_RAW_URL . '/' . AM_UPDATE_BRANCH . AM_UPDATE_REPO_VERSION_FILE;

		if ($content = Fetch::get($versionFileUrl)) {
			$version = self::extractVersion($content);
		}

		return $version;
	}

	/**
	 * Get items to be updated from config.
	 *
	 * @return array The array of items to be updated or false on error
	 */
	public static function items() {
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
	 * Run the actual update.
	 *
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public static function run(Messenger $Messenger) {
		self::$timestamp = date('Ymd-His');
		self::log('Starting update ' . date('c'));
		self::preloadClasses();
		$items = self::items();

		if (!$items) {
			$Messenger->setError(Text::get('systemUpdateItemsError'));

			return false;
		}

		if ($phpVersion = self::higherPHPVersionRequired()) {
			$Messenger->setError(Text::get('systemUpdatePhpVersionError') . " $phpVersion+.");

			return false;
		}

		if (!self::permissionsGranted($items)) {
			$Messenger->setError(Text::get('systemUpdatePermissionError'));

			return false;
		}

		self::log('Version to be updated: ' . AM_VERSION);
		Debug::log('Version to be updated: ' . AM_VERSION);
		self::log('Updating items: ' . implode(', ', $items));

		$archive = self::getArchive();

		if (!$archive) {
			$Messenger->setError(Text::get('systemUpdateDownloadError'));

			return false;
		}

		if (!self::backupCurrent($items)) {
			$Messenger->setError(Text::get('systemUpdateFailedError'));

			return false;
		}

		if (!self::unpack($archive, $items)) {
			$Messenger->setError(Text::get('systemUpdateFailedError'));

			return false;
		}

		if (file_exists(AM_FILE_SITE_MTIME)) {
			unlink(AM_FILE_SITE_MTIME);
		}

		$version = '';
		$versionFile = AM_BASE_DIR . '/automad/version.php';

		if (is_readable($versionFile)) {
			$version = self::extractVersion(file_get_contents($versionFile));
		}

		self::log('Successfully updated Automad to version ' . $version);
		Debug::log('Successfully updated Automad to version ' . $version);
		$Messenger->setData(array('current' => $version, 'state' => 'success'));
		$Messenger->setSuccess(Text::get('systemUpdateSuccess'));

		return true;
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
	 * @param string $str
	 * @return bool True on success, false on error
	 */
	private static function backupCurrent(array $items) {
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
		$downloadUrl = AM_UPDATE_REPO_DOWNLOAD_URL . '/' . AM_UPDATE_BRANCH . '.zip';
		$archive = AM_BASE_DIR . AM_UPDATE_TEMP . '/download/' . self::$timestamp . '.zip';

		FileSystem::makeDir(dirname($archive));

		if (!Fetch::download($downloadUrl, $archive)) {
			$archive = false;
			self::log('Download failed!');
		}

		return $archive;
	}

	/**
	 * Check if the server's PHP version matches the minimum requirements in the remote composer.json file.
	 *
	 * @return string a version number in case PHP is outdated or an empty string
	 */
	private static function higherPHPVersionRequired() {
		$requiredVersion = '';
		$composerFileUrl = AM_UPDATE_REPO_RAW_URL . '/' . AM_UPDATE_BRANCH . '/composer.json';

		$composerJson = Fetch::get($composerFileUrl);

		if (!$composerJson) {
			return $requiredVersion;
		}

		try {
			$data = json_decode($composerJson);
			$composerRequiredVersion = preg_replace('/[^\d\.]*/', '', $data->require->php);

			self::log("The required PHP version is $composerRequiredVersion");
			Debug::log("The required PHP version is $composerRequiredVersion");

			if (version_compare(PHP_VERSION, $composerRequiredVersion, '<')) {
				$requiredVersion = $composerRequiredVersion;
				self::log("The server's PHP version in outdated!");
			}
		} catch (\Exception $e) {
			self::log($e->getMessage());
		}

		return $requiredVersion;
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
			$temp = $item . '.' . crc32($item);

			if (!@rename($item, $temp)) {
				return false;
			}

			rename($temp, $item);
		}

		return true;
	}

	/**
	 * Preload required classes before removing old installation.
	 */
	private static function preloadClasses() {
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