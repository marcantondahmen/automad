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
 * Copyright (c) 2013-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A collection of file utilities.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2013-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class FileUtils {
	/**
	 * Return an array with the allowed file types.
	 *
	 * @return array An array of file types
	 */
	public static function allowedFileTypes() {
		return Parse::csv(AM_ALLOWED_FILE_TYPES);
	}

	/**
	 * Read a file's caption file and render contained markdown syntax.
	 *
	 * The caption filename is build out of the actual filename with the appended ".caption" extension, like "image.jpg.caption".
	 *
	 * @param string $file
	 * @return string The caption string
	 */
	public static function caption(string $file) {
		// Build filename of the caption file.
		$captionFile = $file . '.' . AM_FILE_EXT_CAPTION;
		Debug::log($captionFile);

		if (is_readable($captionFile)) {
			return file_get_contents($captionFile);
		}

		return '';
	}

	/**
	 * Parse a file declaration string where multiple glob patterns or URLs can be separated by a comma
	 * and return an array with the resolved/downloaded file paths.
	 * If $stripBaseDir is true, the base directory will be stripped from the path
	 * and each path gets resolved to be relative to the Automad installation directory.
	 *
	 * @param string $str
	 * @param Page $Page
	 * @param bool $stripBaseDir
	 * @return array An array with resolved file paths
	 */
	public static function fileDeclaration(string $str, Page $Page, bool $stripBaseDir = false) {
		$files = array();

		if ($str) {
			foreach (Parse::csv($str) as $item) {
				if (preg_match('/\:\/\//is', $item)) {
					$RemoteFile = new RemoteFile($item);

					if ($file = $RemoteFile->getLocalCopy()) {
						$files[] = $file;
					}
				} elseif ($f = FileSystem::glob(Resolve::filePath($Page->path, $item))) {
					$f = array_filter($f, '\Automad\Core\FileSystem::isAllowedFileType');
					$files = array_merge($files, $f);
				}
			}

			if ($stripBaseDir) {
				array_walk($files, function (&$file) {
					$file = Str::stripStart($file, AM_BASE_DIR);
				});
			}
		}

		return $files;
	}

	/**
	 * Parse a filename to check whether a file is an image or not.
	 *
	 * @param string $file
	 * @return bool True if $file is an image file
	 */
	public static function fileIsImage(string $file) {
		return (in_array(FileSystem::getExtension($file), array('jpg', 'jpeg', 'png', 'gif')));
	}
}
