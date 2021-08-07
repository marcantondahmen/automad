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
use Automad\Core\Str;
use Automad\UI\Response;
use Automad\UI\Utils\FileSystem;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The file collection model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class FileCollection {
	/**
	 * Delete files.
	 *
	 * @param array $files
	 * @param string $path
	 * @return \Automad\UI\Response the response object
	 */
	public static function deleteFiles($files, $path) {
		$Response = new Response();

		// Check if directory is writable.
		if (is_writable($path)) {
			$success = array();
			$errors = array();

			foreach ($files as $f) {
				// Make sure submitted filename has no '../' (basename).
				$file = $path . basename($f);

				if ($error = FileSystem::deleteMedia($file)) {
					$errors[] = $error;
				} else {
					$success[] = '"' . basename($file) . '"';
				}
			}

			Cache::clear();

			$Response->setSuccess(Text::get('success_remove') . '<br />' . implode('<br />', $success));
			$Response->setError(implode('<br />', $errors));
		} else {
			$Response->setError(Text::get('error_permission') . ' "' . basename($path) . '"');
		}

		return $Response;
	}

	/**
	 * Upload model.
	 *
	 * @param array $files
	 * @param string $path
	 * @return string an error message or false on success
	 */
	public static function upload($files, $path) {
		$error = '';

		// Move uploaded files
		if (isset($files['files']['name'])) {
			// Check if upload destination is writable.
			if (is_writable($path)) {
				$errors = array();

				// In case the $files array consists of multiple files (IE uploads!).
				for ($i = 0; $i < count($files['files']['name']); $i++) {
					// Check if file has a valid filename (allowed file type).
					if (FileSystem::isAllowedFileType($files['files']['name'][$i])) {
						$newFile = $path . Str::sanitize($files['files']['name'][$i]);
						move_uploaded_file($files['files']['tmp_name'][$i], $newFile);
					} else {
						$errors[] = Text::get('error_file_format') . ' "' .
									FileSystem::getExtension($files['files']['name'][$i]) . '"';
					}
				}

				Cache::clear();

				if ($errors) {
					$error = implode('<br />', $errors);
				}
			} else {
				$error = Text::get('error_permission') . ' "' . basename($path) . '"';
			}
		}

		return $error;
	}
}
