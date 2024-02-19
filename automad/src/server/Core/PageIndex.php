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
 * Copyright (c) 2022-2023 by Marc Anton Dahmen
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
 * @copyright Copyright (c) 2022-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PageIndex {
	const FILENAME = 'index';

	/**
	 * Append a new page to the index in case the index exists.
	 *
	 * @param string $parentPath
	 * @param string $path
	 */
	public static function append(string $parentPath, string $path): void {
		$layout = self::read($parentPath);
		$layout[] = basename($path);

		self::write($parentPath, $layout);
	}

	/**
	 * Read the directory index from an index file and return the index as array.
	 *
	 * @param string $parentPath
	 * @return array the index
	 */
	public static function read(string $parentPath): array {
		$indexFile = self::getIndexFile($parentPath);

		if (is_readable($indexFile)) {
			return FileSystem::readJson($indexFile);
		}

		return array();
	}

	/**
	 * Remove a page path from the index.
	 *
	 * @param string $parentPath
	 * @param string $path
	 */
	public static function remove(string $parentPath, string $path): void {
		$layout = self::read($parentPath);

		if ($layout) {
			$index = array_search(basename($path), $layout);

			if ($index !== false) {
				unset($layout[$index]);

				self::write($parentPath, $layout);
			}
		}
	}

	/**
	 * Replace a path in the index
	 *
	 * @param string $parentPath
	 * @param string $old
	 * @param string $new
	 */
	public static function replace(string $parentPath, string $old, string $new): void {
		$layout = self::read($parentPath);

		if ($layout) {
			$index = array_search(basename($old), $layout);

			if ($index !== false) {
				$layout[$index] = $new;

				self::write($parentPath, $layout);
			}
		}
	}

	/**
	 * Update the index file for a given parent directory of a page.
	 *
	 * @param string $parentPath
	 * @param array $layout
	 * @return array the new layout or null
	 */
	public static function write(string $parentPath, array $layout): array {
		$indexFile = self::getIndexFile($parentPath);

		$layout = array_map(function (string $item): string {
			return basename($item);
		}, $layout);

		if (FileSystem::writeJson($indexFile, array_values($layout))) {
			return $layout;
		}

		return array();
	}

	/**
	 * Get the index file associated with a given parent directory.
	 *
	 * @param string $parentPath
	 * @return string
	 */
	private static function getIndexFile(string $parentPath): string {
		$parentPath = rtrim($parentPath, '/') . '/';

		return AM_BASE_DIR . AM_DIR_PAGES . $parentPath . PageIndex::FILENAME;
	}
}
