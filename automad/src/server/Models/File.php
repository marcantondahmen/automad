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
 * Copyright (c) 2021-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Models;

use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\Messenger;
use Automad\Core\Request;
use Automad\Core\Str;
use Automad\Core\Text;
use Automad\Stores\DataStore;
use Automad\System\DiskUsage;
use Automad\System\Fetch;
use Automad\System\FileSystem;
use Automad\System\FileUtils;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The file model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class File {
	/**
	 * Edit file information (file name and caption).
	 *
	 * @param string $newName
	 * @param string $oldName
	 * @param string $caption
	 * @param Messenger $Messenger
	 * @return bool true in case a file hase been renamed
	 */
	public static function editInfo(string $newName, string $oldName, string $caption, Messenger $Messenger): bool {
		if (!$oldName || !$newName) {
			$Messenger->setError(Text::get('invalidFormError'));

			return false;
		}

		$Automad = Automad::fromCache();
		$path = FileSystem::getPathByPostUrl($Automad);
		$oldFile = $path . basename($oldName);
		$extension = FileSystem::getExtension($oldFile);
		$newFile = $path . Str::slug(basename(preg_replace('/\.' . $extension . '$/i', '', $newName) ?? '')) . '.' . $extension;

		if (!FileSystem::isAllowedFileType($newFile)) {
			$Messenger->setError(Text::get('unsupportedFileTypeError') . ' "' . FileSystem::getExtension($newFile) . '"');

			return false;
		}

		$softReload = false;

		// Rename file and caption if needed and update all file links.
		if ($newFile != $oldFile) {
			if (FileSystem::renameMedia($oldFile, $newFile, $Messenger)) {
				$softReload = true;

				Links::update(
					$Automad,
					Str::stripStart($oldFile, AM_BASE_DIR),
					Str::stripStart($newFile, AM_BASE_DIR)
				);

				if ($Page = $Automad->getPage(Request::post('url'))) {
					// In case there is a posted URL, also rename files that have been added with only
					// basename since they belong to the same page.
					Links::update(
						$Automad,
						basename($oldFile),
						basename($newFile),
						$Page->path
					);
				}
			}
		}

		// Write caption.
		if (!$Messenger->getError() && FileUtils::fileIsImage($newFile)) {
			$newCaptionFile = $newFile . '.' . FileUtils::EXT_CAPTION;

			// Only if file exists already or $caption is empty.
			if (is_writable($newCaptionFile) || !file_exists($newCaptionFile)) {
				FileSystem::write($newCaptionFile, $caption);
			} else {
				$Messenger->setError(Text::get('couldNotSaveError') . ' "' . basename($newCaptionFile) . '"');
			}
		}

		Cache::clear();

		return $softReload;
	}

	/**
	 * Import file from URL.
	 *
	 * @param string $importUrl
	 * @param string $pageUrl
	 * @param Messenger $Messenger
	 * @return string the final file name of the imported file or an empty string in case of an error
	 */
	public static function import(string $importUrl, string $pageUrl, Messenger $Messenger): string {
		if (!$importUrl) {
			$Messenger->setError(Text::get('missingUrlError'));

			return '';
		}

		// Resolve local URLs.
		if (strpos($importUrl, '/') === 0) {
			$importUrl = AM_SERVER . AM_BASE_URL . $importUrl;

			Debug::log($importUrl, 'Local URL');
		}

		$data = Fetch::request($importUrl);

		if (empty($data)) {
			$Messenger->setError(Text::get('importFailedError'));

			return '';
		}

		if ($pageUrl) {
			$Automad = Automad::fromCache();
			$Page = $Automad->getPage($pageUrl);

			if (!$Page) {
				$Messenger->setError(Text::get('pageNotFoundError'));

				return '';
			}

			$dir = AM_BASE_DIR . AM_DIR_PAGES . $Page->path;
		} else {
			$dir = AM_BASE_DIR . AM_DIR_SHARED . '/';
		}

		$fileName = self::save($dir, preg_replace('/\?.*/', '', basename($importUrl)) ?? '', $data, $Messenger);

		if ($fileName) {
			DiskUsage::refresh();
			Cache::clear();
		}

		return $fileName;
	}

	/**
	 * Save data as a file in a given directory. The file name is slugged and validated before anything
	 * is written to the directory. In case the name has no allowed file extension, the extension is
	 * derived from the image MIME type of the data, if possible.
	 *
	 * @param string $dir
	 * @param string $name
	 * @param string $data
	 * @param Messenger $Messenger
	 * @return string the final file name of the saved file or an empty string in case of an error
	 */
	public static function save(string $dir, string $name, string $data, Messenger $Messenger): string {
		$fileName = Str::slug($name);

		if (!FileSystem::isAllowedFileType($fileName)) {
			$fileName .= FileSystem::getImageExtensionFromData($data);
		}

		if (!FileSystem::isAllowedFileType($fileName) || self::isReservedFileName($fileName)) {
			$Messenger->setError(Text::get('unsupportedFileTypeError') . ' "' . $fileName . '"');

			return '';
		}

		if (!FileSystem::write($dir . $fileName, $data)) {
			$Messenger->setError(Text::get('couldNotSaveError') . ' "' . $fileName . '"');

			return '';
		}

		return $fileName;
	}

	/**
	 * Check whether a file name is reserved for page control files or hidden files.
	 *
	 * @param string $fileName
	 * @return bool
	 */
	private static function isReservedFileName(string $fileName): bool {
		return $fileName === '' || $fileName === DataStore::FILENAME || str_starts_with($fileName, '.');
	}
}
