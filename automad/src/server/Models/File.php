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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Models;

use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\FileUtils;
use Automad\Core\Messenger;
use Automad\Core\Request;
use Automad\Core\Str;
use Automad\Core\Text;
use Automad\System\Fetch;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The file model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
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
		if (!$Messenger->getError()) {
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
	 * @return bool true on success
	 */
	public static function import(string $importUrl, string $pageUrl, Messenger $Messenger): bool {
		if (!$importUrl) {
			$Messenger->setError(Text::get('missingUrlError'));

			return false;
		}

		// Resolve local URLs.
		if (strpos($importUrl, '/') === 0) {
			$importUrl = AM_SERVER . AM_BASE_URL . $importUrl;

			Debug::log($importUrl, 'Local URL');
		}

		$data = Fetch::get($importUrl);

		if (empty($data)) {
			$Messenger->setError(Text::get('importFailedError'));

			return false;
		}

		$fileName = Str::slug(preg_replace('/\?.*/', '', basename($importUrl)) ?? '');

		if ($pageUrl) {
			$Automad = Automad::fromCache();
			$Page = $Automad->getPage($pageUrl);

			if (!$Page) {
				return false;
			}

			$path = AM_BASE_DIR . AM_DIR_PAGES . $Page->path . $fileName;
		} else {
			$path = AM_BASE_DIR . AM_DIR_SHARED . '/' . $fileName;
		}

		FileSystem::write($path, $data);
		Cache::clear();

		if (!FileSystem::isAllowedFileType($path)) {
			$newPath = $path . FileSystem::getImageExtensionFromMimeType($path);

			if (FileSystem::isAllowedFileType($newPath)) {
				if (!FileSystem::renameMedia($path, $newPath, $Messenger)) {
					return false;
				}
			} else {
				unlink($path);
				$Messenger->setError(Text::get('unsupportedFileTypeError'));

				return false;
			}
		}

		return true;
	}
}
