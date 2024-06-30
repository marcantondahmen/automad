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
 * Copyright (c) 2013-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Automad\Engine\PatternAssembly;
use Automad\Models\Page;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Parse class holds all parsing methods.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2013-2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Parse {
	const STRING_SEPARATOR = ',';

	/**
	 * Please use `FileUtils::caption()` instead.
	 *
	 * @see FileUtils::caption()
	 * @deprecated 1.9.0
	 * @param string $file
	 * @return string the caption string
	 */
	public static function caption(string $file): string {
		return FileUtils::caption($file);
	}

	/**
	 * Split and trim comma separated string.
	 *
	 * @param string $str
	 * @return array The array of separate and trimmed strings
	 */
	public static function csv(string $str): array {
		$array = explode(self::STRING_SEPARATOR, $str);
		$array = array_filter($array, 'strlen');

		return array_map('trim', $array);
	}

	/**
	 * Please use `FileUtils::fileDeclaration()` instead.
	 *
	 * @see FileUtils::fileDeclaration()
	 * @deprecated 1.9.0
	 * @param string $str
	 * @param Page $Page
	 * @param bool $stripBaseDir
	 * @return array An array with resolved file paths
	 */
	public static function fileDeclaration(string $str, Page $Page, bool $stripBaseDir = false): array {
		return FileUtils::fileDeclaration($str, $Page, $stripBaseDir);
	}

	/**
	 * Parse a (dirty) JSON string and return an associative, filtered array
	 *
	 * @param string $str
	 * @return array $options - associative array
	 */
	public static function jsonOptions(string $str): array {
		$options = array();
		$debug = array();

		if ($str) {
			$debug['String'] = $str;

			// Remove all tabs and newlines.
			$str = str_replace(array("\n", "\r", "\t"), ' ', $str);

			// Clean up "dirty" JSON by replacing single with double quotes and
			// wrapping all keys in double quotes.
			$pairs = array();
			preg_match_all('/' . PatternAssembly::keyValue() . '/s', $str, $matches, PREG_SET_ORDER);

			foreach ($matches as $match) {
				$key = '"' . trim($match['key'], '"') . '"';
				$value = preg_replace('/^([\'"])(.*)\1$/s', '$2', trim($match['value']));

				if (!is_numeric($value) && $value !== 'true' && $value !== 'false') {
					$value = str_replace('\"', '"', $value);
					$value = addcslashes($value, '"');
					$value = '"' . $value . '"';
				}

				$pairs[] = $key . ':' . $value;
			}

			// Build valid JSON string.
			$str = '{' . implode(',', $pairs) . '}';

			$debug['Clean'] = $str;

			// Decode JSON.
			$options = json_decode($str, true);

			// Remove all undefined items (empty string).
			// It is not possible to use array_filter($options, 'strlen') here, since an array item could be an array itself and strlen() only expects strings.
			if (is_array($options)) {
				$options = 	array_filter($options, function ($value) {
					return ($value !== '');
				});
			} else {
				$options = array();
			}

			$debug['JSON'] = $options;
			Debug::log($debug);
		}

		return $options;
	}
}
