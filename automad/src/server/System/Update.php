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
 * Copyright (c) 2017-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\System;

use Automad\App;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\Messenger;
use Automad\Core\Parse;
use Automad\Core\Str;
use Automad\Core\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Update class handles the process of updating Automad using the dashboard.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2017-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Update {
	const DOWNLOAD_URL = 'https://github.com/' . AM_UPDATE_REPO . '/archive/' . AM_UPDATE_BRANCH . '.zip';
	const RAW_URL = 'https://raw.githubusercontent.com/' . AM_UPDATE_REPO . '/' . AM_UPDATE_BRANCH;

	/**
	 * The update timestamp.
	 */
	private static string $timestamp;

	/**
	 * Download version file and extract version number.
	 *
	 * @return string Version number or false on error.
	 */
	public static function getVersion(): string {
		$version = trim(Fetch::get(Update::RAW_URL . '/VERSION'));

		Debug::log($version);

		return $version;
	}

	/**
	 * Get items to be updated from config.
	 *
	 * @return array The array of items to be updated or false on error
	 */
	public static function items(): array {
		return array_filter(Parse::csv(AM_UPDATE_ITEMS));
	}

	/**
	 * Run the actual update.
	 *
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public static function run(Messenger $Messenger): bool {
		Debug::log(date('Ymd-His'), 'Start Update');
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

		self::log('Version to be updated: ' . App::VERSION);
		Debug::log('Version to be updated: ' . App::VERSION);
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

		Cache::clear();

		$version = '';
		$versionFile = AM_BASE_DIR . '/automad/src/server/App.php';

		if (is_readable($versionFile)) {
			$versionFileContent = file_get_contents($versionFile);
			preg_match("/VERSION\s=\s'([^']+)'/is", $versionFileContent, $matches);
			$version = $matches[1] ?? '';
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
	public static function supported(): bool {
		return (function_exists('curl_version') && class_exists('ZipArchive'));
	}

	/**
	 * Move currently installed items to /cache/update/backup.
	 *
	 * @param array $items
	 * @param string $str
	 * @return bool True on success, false on error
	 */
	private static function backupCurrent(array $items): bool {
		$backup = AM_UPDATE_TEMP . '/backup/' . self::$timestamp;

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
	 * Download zip-archive to be installed.
	 *
	 * @return string|null Path to the downloaded archive or null on error
	 */
	private static function getArchive(): ?string {
		$archive = AM_UPDATE_TEMP . '/download/' . self::$timestamp . '.zip';

		Debug::log($archive, 'Archive');
		FileSystem::makeDir(dirname($archive));

		if (!Fetch::download(Update::DOWNLOAD_URL, $archive)) {
			$archive = null;
			self::log('Download failed!');
		}

		return $archive;
	}

	/**
	 * Check if the server's PHP version matches the minimum requirements in the remote composer.json file.
	 *
	 * @return string a version number in case PHP is outdated or an empty string
	 */
	private static function higherPHPVersionRequired(): string {
		$requiredVersion = '';
		$composerFileUrl = Update::RAW_URL . '/composer.json';

		$composerJson = Fetch::get($composerFileUrl);

		if (!$composerJson) {
			return $requiredVersion;
		}

		try {
			$data = json_decode($composerJson);
			/** @var string */
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
	private static function log(string $data): string {
		$file = AM_UPDATE_TEMP . '/' . self::$timestamp . '.log';
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
	private static function permissionsGranted(array $items): bool {
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
	private static function preloadClasses(): void {
		Text::getObject();
		Cache::clear();
	}

	/**
	 * Unpack all item matching AM_UPDATE_ITEM.
	 *
	 * @param string $archive
	 * @param array $items
	 * @return bool True on success, false on error
	 */
	private static function unpack(string $archive, array $items): bool {
		$success = true;
		$zip = new \ZipArchive();
		$itemsMatchRegex = '/^[\w\-]+(' . addcslashes(implode('|', $items), '/') . ')/';

		if ($zip->open($archive)) {
			// Iterate over zip entries and unpack item in case
			// the name matches on of the update items.
			for ($i = 0; $i < $zip->numFiles; $i++) {
				$name = $zip->getNameIndex($i);

				if (preg_match($itemsMatchRegex, $name) && !str_ends_with($name, '/')) {
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
