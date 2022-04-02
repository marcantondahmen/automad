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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Models;

use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\Request;
use Automad\Core\Str;
use Automad\UI\Utils\FileSystem;
use Automad\UI\Utils\Messenger;
use Automad\UI\Utils\Text;
use Automad\UI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The file model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class FileModel {
	/**
	 * Edit file information (file name and caption).
	 *
	 * @param string $newName
	 * @param string $oldName
	 * @param string $caption
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public static function editInfo(string $newName, string $oldName, string $caption, Messenger $Messenger) {
		if (!$oldName || !$newName) {
			$Messenger->setError(Text::get('error_form'));

			return false;
		}

		$Automad = UICache::get();
		$path = FileSystem::getPathByPostUrl($Automad);
		$oldFile = $path . basename($oldName);
		$extension = FileSystem::getExtension($oldFile);
		$newFile = $path . Str::slug(basename(preg_replace('/\.' . $extension . '$/i', '', $newName))) . '.' . $extension;

		if (!FileSystem::isAllowedFileType($newFile)) {
			$Messenger->setError(Text::get('error_file_format') . ' "' . FileSystem::getExtension($newFile) . '"');

			return false;
		}

		// Rename file and caption if needed and update all file links.
		if ($newFile != $oldFile) {
			if (FileSystem::renameMedia($oldFile, $newFile, $Messenger)) {
				LinksModel::update(
					$Automad,
					Str::stripStart($oldFile, AM_BASE_DIR),
					Str::stripStart($newFile, AM_BASE_DIR)
				);

				if ($Page = $Automad->getPage(Request::post('url'))) {
					// In case there is a posted URL, also rename files that have been added with only
					// basename since they belong to the same page.
					$file = AM_DIR_PAGES . $Page->path . $Page->template . '.' . AM_FILE_EXT_DATA;

					LinksModel::update(
						$Automad,
						basename($oldFile),
						basename($newFile),
						$file
					);
				}
			}
		}

		// Write caption.
		if (!$Messenger->getError()) {
			$newCaptionFile = $newFile . '.' . AM_FILE_EXT_CAPTION;

			// Only if file exists already or $caption is empty.
			if (is_writable($newCaptionFile) || !file_exists($newCaptionFile)) {
				FileSystem::write($newCaptionFile, $caption);
			} else {
				$Messenger->setError(Text::get('error_file_save') . ' "' . basename($newCaptionFile) . '"');
			}
		}

		Cache::clear();

		return true;
	}

	/**
	 * Import file from URL.
	 *
	 * @param string $importUrl
	 * @param string $pageUrl
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public static function import(string $importUrl, string $pageUrl, Messenger $Messenger) {
		if (!$importUrl) {
			$Messenger->setError(Text::get('error_no_url'));

			return false;
		}

		// Resolve local URLs.
		if (strpos($importUrl, '/') === 0) {
			if (getenv('HTTPS') && getenv('HTTPS') !== 'off' && getenv('HTTP_HOST')) {
				$protocol = 'https://';
			} else {
				$protocol = 'http://';
			}

			$importUrl = $protocol . getenv('HTTP_HOST') . AM_BASE_URL . $importUrl;
			Debug::log($importUrl, 'Local URL');
		}

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_URL, $importUrl);

		$data = curl_exec($curl);

		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200 || curl_errno($curl)) {
			$Messenger->setError(Text::get('error_import'));
			curl_close($curl);

			return false;
		} else {
			curl_close($curl);
		}

		$fileName = Str::slug(preg_replace('/\?.*/', '', basename($importUrl)));

		if ($pageUrl) {
			$Automad = UICache::get();
			$Page = $Automad->getPage($pageUrl);
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
				$Messenger->setError(Text::get('error_file_format'));

				return false;
			}
		}

		return true;
	}
}
