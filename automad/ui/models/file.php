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
use Automad\Core\Str;
use Automad\UI\Utils\FileSystem;
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
class File {
	/**
	 * Edit file information (file name and caption).
	 *
	 * @param string $newName
	 * @param string $oldName
	 * @param string $caption
	 * @return string an error message or false on success
	 */
	public static function editInfo($newName, $oldName, $caption) {
		$error = '';

		if ($oldName && $newName) {
			$Automad = UICache::get();
			$path = FileSystem::getPathByPostUrl($Automad);
			$oldFile = $path . basename($oldName);
			$newFile = $path . Str::sanitize(basename($newName));

			if (FileSystem::isAllowedFileType($newFile)) {
				// Rename file and caption if needed.
				if ($newFile != $oldFile) {
					$error = FileSystem::renameMedia($oldFile, $newFile);
				}

				// Write caption.
				if (empty($output['error'])) {
					$newCaptionFile = $newFile . '.' . AM_FILE_EXT_CAPTION;

					// Only if file exists already or $caption is empty.
					if (is_writable($newCaptionFile) || !file_exists($newCaptionFile)) {
						FileSystem::write($newCaptionFile, $caption);
					} else {
						$error = Text::get('error_file_save') . ' "' . basename($newCaptionFile) . '"';
					}
				}

				Cache::clear();
			} else {
				$error = Text::get('error_file_format') . ' "' . FileSystem::getExtension($newFile) . '"';
			}
		} else {
			$error = Text::get('error_form');
		}

		return $error;
	}

	/**
	 * Import file from URL.
	 *
	 * @param string $importUrl
	 * @param string $pageUrl
	 * @return array an error message or false on success
	 */
	public static function import($importUrl, $pageUrl = false) {
		$error = '';

		if ($importUrl) {
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
				$error = Text::get('error_import');
			} else {
				$fileName = Str::sanitize(preg_replace('/\?.*/', '', basename($importUrl)));

				if ($pageUrl) {
					$Automad = UICache::get();
					$Page = $Automad->getPage($pageUrl);
					$path = AM_BASE_DIR . AM_DIR_PAGES . $Page->path . $fileName;
				} else {
					$path = AM_BASE_DIR . AM_DIR_SHARED . '/' . $fileName;
				}

				FileSystem::write($path, $data);
				Cache::clear();
			}

			curl_close($curl);

			if (!FileSystem::isAllowedFileType($path)) {
				$newPath = $path . FileSystem::getImageExtensionFromMimeType($path);

				if (FileSystem::isAllowedFileType($newPath)) {
					FileSystem::renameMedia($path, $newPath);
				} else {
					unlink($path);
					$error = Text::get('error_file_format');
				}
			}
		} else {
			$error = Text::get('error_no_url');
		}

		return $error;
	}
}
