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

use Automad\Engine\PatternAssembly;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Parse class holds all parsing methods.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2013-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Parse {
	/**
	 * Please use `FileUtils::caption()` instead.
	 *
	 * @see FileUtils::caption()
	 * @deprecated 1.9.0
	 * @param string $file
	 * @return array An array with resolved file paths
	 */
	public static function caption(string $file) {
		return FileUtils::caption($file);
	}

	/**
	 * Split and trim comma separated string.
	 *
	 * @param string $str
	 * @return array The array of separate and trimmed strings
	 */
	public static function csv(string $str) {
		$array = explode(AM_PARSE_STR_SEPARATOR, $str);
		$array = array_filter($array, 'strlen');

		return array_map('trim', $array);
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
	public static function dataFile(string $file) {
		$vars = array();

		if (!file_exists($file)) {
			return $vars;
		}

		// Get file content and normalize line breaks.
		$content = preg_replace('/\r\n?/', "\n", file_get_contents($file));

		// Split $content into data blocks on every line only containing one or more AM_PARSE_BLOCK_SEPARATOR and whitespace, followed by a key in a new line.
		$pairs = preg_split(
			'/\n' . preg_quote(AM_PARSE_BLOCK_SEPARATOR) . '+\s*\n(?=' . PatternAssembly::$charClassTextFileVariables . '+' . preg_quote(AM_PARSE_PAIR_SEPARATOR) . ')/s',
			$content,
			-1,
			PREG_SPLIT_NO_EMPTY
		);

		// Split $pairs into an array of vars.
		foreach ($pairs as $pair) {
			list($key, $value) = explode(AM_PARSE_PAIR_SEPARATOR, $pair, 2);
			$vars[trim($key)] = trim($value);
		}

		// Remove undefined (empty) items.
		$vars = array_filter($vars, 'strlen');

		return $vars;
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
	public static function fileDeclaration(string $str, Page $Page, bool $stripBaseDir = false) {
		return FileUtils::fileDeclaration($str, $Page, $stripBaseDir);
	}

	/**
	 * Parse a (dirty) JSON string and return an associative, filtered array
	 *
	 * @param string $str
	 * @return array $options - associative array
	 */
	public static function jsonOptions(string $str) {
		$options = array();

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
