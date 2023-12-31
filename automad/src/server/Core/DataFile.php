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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Automad\Engine\PatternAssembly;
use Automad\Models\Page;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The DataFile class handles the reading of JSON formatted data files.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class DataFile {
	const FILENAME = 'data';

	/**
	 * Get the data file path for a given page or the shared data.
	 *
	 * @param Page|null $Page
	 * @return string
	 */
	public static function getFile(?Page $Page = null): string {
		if ($Page) {
			return FileSystem::fullPagePath($Page->path) . self::FILENAME;
		}

		return AM_BASE_DIR . AM_DIR_SHARED . '/' . self::FILENAME;
	}

	/**
	 * Read shared data from disk. If a page path is given, read page data instead.
	 *
	 * @param string|null $pagePath
	 * @return array|null
	 */
	public static function read(?string $pagePath = null): ?array {
		$path = !is_null($pagePath) ? AM_DIR_PAGES . $pagePath : AM_DIR_SHARED;
		$path = rtrim(AM_BASE_DIR . $path, '/') . '/';
		$jsonFile = $path . self::FILENAME;

		if (is_readable($jsonFile)) {
			return (array) FileSystem::readJson($jsonFile, false);
		}

		return null;
	}

	/**
	 * Write shared data to disk. in case a page is given, write page data instead.
	 *
	 * @param array $data
	 * @param string|null $pagePath
	 * @return bool
	 */
	public static function write(array $data, ?string $pagePath = null): bool {
		$data = array_map(function ($value) {
			if (is_string($value)) {
				return trim($value);
			}

			return $value;
		}, $data);

		$data = array_filter($data, function ($value) {
			if (is_string($value)) {
				return strlen($value);
			}

			return true;
		});

		$path = !is_null($pagePath) ? AM_DIR_PAGES . $pagePath : AM_DIR_SHARED;
		$file = rtrim(AM_BASE_DIR . $path, '/') . '/' . self::FILENAME;

		return FileSystem::writeJson($file, $data);
	}
}
