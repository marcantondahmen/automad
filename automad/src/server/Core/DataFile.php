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
 * The DataFile class handles the reading of the new JSON formatted files and the legacy data file format.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class DataFile {
	const FILENAME = '.data.json';

	/**
	 * Get the data file path for a given page or the shared data.
	 * Legacy formats are supported as well.
	 *
	 * @param Page|null $Page
	 * @return string
	 */
	public static function getFile(?Page $Page = null): string {
		$jsonFile = AM_BASE_DIR . AM_DIR_SHARED . '/' . self::FILENAME;
		$legacyFile = AM_BASE_DIR . AM_DIR_SHARED . '/data.txt';

		if ($Page) {
			$jsonFile = FileSystem::fullPagePath($Page->path) . self::FILENAME;
			$legacyFile = FileSystem::fullPagePath($Page->path) . $Page->template . '.txt';
		}

		if (is_readable($jsonFile)) {
			return $jsonFile;
		}

		return $legacyFile;
	}

	/**
	 * Read a shared data from disk. If a page path is given, read page data instead.
	 * Legacy formats are supported as well.
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

		if (is_null($pagePath)) {
			$data = self::readLegacyFormat($path . 'data.txt');

			self::write($data, $pagePath);
			unlink($path . 'data.txt');

			return $data;
		}

		if ($files = FileSystem::glob("{$path}*.txt")) {
			$legacyFile = $files[0];

			$data = self::readLegacyFormat($legacyFile);
			$data[Fields::TEMPLATE] = str_replace('.txt', '', basename($legacyFile));

			$now = date(Page::DATE_FORMAT);

			$data[Fields::TIME_CREATED] = $now;
			$data[Fields::TIME_LAST_MODIFIED] = $now;

			self::write($data, $pagePath);
			unlink($legacyFile);

			return $data;
		}

		return null;
	}

	/**
	 * Loads and parses a text file.
	 *
	 * First it separates the different blocks into simple key/value pairs.
	 * Then it creates an array of vars by splitting the pairs.
	 *
	 * @param string $file
	 * @return array $vars
	 */
	public static function readLegacyFormat(string $file): array {
		$data = array();

		if (!file_exists($file)) {
			return $data;
		}

		$pairs = preg_split(
			'/\n\-+\s*\n(?=' . PatternAssembly::CHAR_CLASS_EDITABLE_VARS . '+\:)/s',
			preg_replace('/\r\n?/', "\n", file_get_contents($file)),
			-1,
			PREG_SPLIT_NO_EMPTY
		);

		foreach ($pairs as $pair) {
			list($key, $value) = explode(':', $pair, 2);

			if (strlen($value)) {
				$key = trim($key);
				$value = trim($value);

				if (str_starts_with($key, '+')) {
					$value = json_decode($value, false);
				}

				$data[$key] = $value;
			}
		}

		return $data;
	}

	/**
	 * Write shared data to disk. in case a page is given, write page data instead.
	 * Legacy formats are supported as well.
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
