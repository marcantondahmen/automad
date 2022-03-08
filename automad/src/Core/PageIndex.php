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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The PageIndex class handles reading and writing index files.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PageIndex {
	/**
	 * Append a new page to the index in case the index exists.
	 *
	 * @param string $parentPath
	 * @param string $path
	 */
	public static function append(string $parentPath, string $path) {
		$layout = self::read($parentPath);

		if ($layout) {
			$layout[] = basename($path);

			return self::write($parentPath, $layout);
		}
	}

	/**
	 * Read the directory index from an index file and return the index as array.
	 *
	 * @param string $parentPath
	 * @return array the index
	 */
	public static function read(string $parentPath) {
		$indexFile = self::getIndexFile($parentPath);

		if (is_readable($indexFile)) {
			return preg_split('/\s+/', file_get_contents($indexFile));
		}

		return array();
	}

	/**
	 * Remove a page path from the index.
	 *
	 * @param string $parentPath
	 * @param string $path
	 */
	public static function remove(string $parentPath, string $path) {
		$layout = self::read($parentPath);

		if ($layout) {
			$index = array_search(basename($path), $layout);

			if ($index !== false) {
				unset($layout[$index]);

				return self::write($parentPath, $layout);
			}
		}
	}

	/**
	 * Update the index file for a given parent directory of a page.
	 *
	 * @param string $parentPath
	 * @param array $layout
	 * @return bool true on success
	 */
	public static function write(string $parentPath, array $layout) {
		$indexFile = self::getIndexFile($parentPath);

		$layout = array_map(function ($item) {
			return basename($item);
		}, $layout);

		if (FileSystem::write($indexFile, implode("\r\n", $layout))) {
			return $layout;
		}
	}

	/**
	 * Get the index file associated with a given parent directory.
	 *
	 * @param string $parentPath
	 */
	private static function getIndexFile(string $parentPath) {
		$parentPath = rtrim($parentPath, '/') . '/';

		return AM_BASE_DIR . AM_DIR_PAGES . $parentPath . AM_FILE_INDEX;
	}
}
