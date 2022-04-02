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
 * Copyright (c) 2016-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Michelf\MarkdownExtra;
use URLify;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Str class holds all string methods.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Str {
	/**
	 * Change format of a given date, optionally according to locale settings.
	 *
	 * In case a date variable is set in a txt file, its format can be different to a timestamp (mtime) of a file or page.
	 * To be independent on the given format without explicitly specifying it, strtotime() is used generate a proper input date.
	 * To use DateTime::createFromFormat() instead would require a third parameter (the original format)
	 * and would therefore make things more complicated than needed.
	 * The format can use either the date() or ICU syntax. In case a locale is defined,
	 * the ICU syntax is used.
	 *
	 * @see https://www.php.net/manual/en/intldateformatter.create.php
	 * @see https://www.php.net/manual/en/intldateformatter.format.php
	 * @see https://unicode-org.github.io/icu/userguide/format_parse/datetime/
	 * @see https://www.php.net/manual/en/datetime.format.php
	 * @param string $date
	 * @param string $format
	 * @param string|null $locale
	 * @return string The formatted date
	 */
	public static function dateFormat(string $date, string $format = 'D, d M Y', ?string $locale = null) {
		if (!$date || !$format) {
			return '';
		}

		$date = strtotime($date);

		if ($locale && class_exists('\IntlDateFormatter')) {
			$formatter = new \IntlDateFormatter(
				$locale,
				\IntlDateFormatter::LONG,
				\IntlDateFormatter::NONE,
				null,
				null,
				$format
			);

			return $formatter->format($date);
		}

		return date($format, $date);
	}

	/**
	 * Set a default value for $str in case $str is empty.
	 *
	 * @param string $str
	 * @param string $defaultValue
	 * @return string The default value
	 */
	public static function def(string $str, string $defaultValue = '') {
		if (trim($str) === '') {
			$str = $defaultValue;
		}

		return $str;
	}

	/**
	 * Escapes a string to be used safely in a JSON string.
	 *
	 * @param string $str
	 * @return string The escaped string
	 */
	public static function escape(string $str) {
		// Escape values to be used in headless mode.
		// The json_encode() function is used to create a valid JSON string
		// with only one temporary key.
		// After getting that JSON string, the key, the brackets and quotes
		// are stripped to get the escaped version of $str.
		$str = preg_replace('/[\r\n]+/', '\n', trim($str));
		$str = json_encode(array('temp' => $str), JSON_UNESCAPED_SLASHES);
		$str = Str::stripStart($str, '{"temp":"');
		$str = Str::stripEnd($str, '"}');

		return $str;
	}

	/**
	 * Find the URL of the first image within rendered HTML markup.
	 *
	 * @param string $str
	 * @return string The URL of the first image or an empty string
	 */
	public static function findFirstImage(string $str) {
		if (!$str) {
			return '';
		}

		preg_match('/<img[^>]+src="([^"]+)"/is', $str, $matches);

		if (!empty($matches[1])) {
			return $matches[1];
		}

		return '';
	}

	/**
	 * Find the first paragraph in rendered HTML and return its inner HTML.
	 *
	 * @param string $str
	 * @return string The inner HTML of the first paragraph or an empty string
	 */
	public static function findFirstParagraph(string $str) {
		if (!$str) {
			return '';
		}

		// First remove any paragraph only containing an image.
		$str = preg_replace('/<p>\s*<img.+?><\/p>/is', '', $str);
		preg_match('/<p\b[^>]*>(.*?)<\/p>/is', $str, $matches);

		if (!empty($matches[1])) {
			return $matches[1];
		}

		return '';
	}

	/**
	 * Parse a markdown string. Optionally skip parsing in case $str is a single line string.
	 *
	 * @param string $str
	 * @param bool $multilineOnly
	 * @return string The parsed string
	 */
	public static function markdown(string $str, $multilineOnly = false) {
		// In case $str has no line breaks and $multilineOnly is enabled, skip parsing.
		if (strpos($str, "\n") === false && $multilineOnly) {
			return $str;
		}

		$str = MarkdownExtra::defaultTransform($str);

		return preg_replace_callback('/\<h(2|3)\>(.*?)\<\/h\1\>/i', function ($matches) {
			$id = self::sanitize(self::stripTags($matches[2]), true, 100);

			return "<h{$matches[1]} id=\"$id\">{$matches[2]}</h{$matches[1]}>";
		}, $str);
	}

	/**
	 * Perform a regex match.
	 *
	 * @param string $str
	 * @param string $regex
	 * @return int 1 or 0
	 */
	public static function match(string $str, string $regex = '') {
		return preg_match($regex, $str);
	}

	/**
	 * Search and replace by regex.
	 *
	 * @param string $str
	 * @param string $regex
	 * @param string $replace
	 * @return string The processed string
	 */
	public static function replace(string $str, string $regex = '', string $replace = '') {
		return preg_replace($regex, $replace, $str);
	}

	/**
	 * Cleans up a string to be used as URL, directory or file name.
	 * The returned string constists of the following characters: "a-z", "0-9", "-" and optional dots ".".
	 * That means, this method is safe to be used with filenames as well, since it keeps by default the dots as suffix separators.
	 *
	 * Note: To produce fully safe prefixes and directory names,
	 * possible dots should be removed by setting $removeDots = true.
	 *
	 * @param string $str
	 * @param bool $removeDots
	 * @param int $maxChars
	 * @return string The sanitized string
	 */
	public static function sanitize(string $str, $removeDots = false, $maxChars = 100) {
		if (strlen($str) === 0) {
			return '';
		}

		// If dots should be removed from $str, replace them with '-', since URLify::filter() only removes them fully without replacing.
		if ($removeDots) {
			$str = str_replace('.', '-', $str);
		}

		// Convert slashes separately to avoid issues with regex in URLify.
		$str = str_replace('/', '-', $str);

		// Convert dashes to simple hyphen.
		$str = str_replace(array('&mdash;', '&ndash;'), '-', $str);

		// Configure URLify.
		// Add non-word chars and reset the remove list.
		// Note: $maps gets directly manipulated without using URLify::add_chars().
		// Using the add_chars() method would extend $maps every time, Str::sanitize() gets called.
		// Adding a new array to $maps using a key avoids that and just overwrites that same array after the first call without adding new elements.
		URLify::$maps['nonWordChars'] = array('=' => '-', '&' => '-and-', '+' => '-plus-', '@' => '-at-', '|' => '-', '*' => '-x-');
		URLify::$remove_list = array();

		// Since all possible dots got removed already above (if $removeDots is true),
		// $str should be filtered as filename to keep dots if they are still in $str and $removeDots is false.
		return URLify::filter($str, $maxChars, '', true);
	}

	/**
	 * Shortens a string keeping full words. Note that this method also first strips all tags from the given string.
	 *
	 * @param string $str
	 * @param int $maxChars
	 * @param string $ellipsis
	 * @return string The shortened string
	 */
	public static function shorten(string $str, $maxChars, string $ellipsis = ' ...') {
		if (strlen($str) === 0) {
			return '';
		}

		$str = Str::stripTags($str);
		$str = preg_replace('/[\n\r]+/s', ' ', $str);

		// Shorten $text to maximal characters (full words).
		if (strlen($str) > $maxChars) {
			// Cut $str to $maxChars +1.
			// Note that it has to be +1 to be able to catch the edge case,
			// where $maxChars is exactly an end of a word. +1 would than
			// be a space.
			$str = substr($str, 0, $maxChars + 1);
			// Find last space and get position.
			$pos = strrpos($str, ' ');
			// Cut $str again at last space's position (< $maxChars).
			$str = substr($str, 0, $pos) . $ellipsis;
		}

		return trim($str);
	}

	/**
	 * Creates a slug for save diretory names, ids or similar from a given string.
	 *
	 * In case the sanitized string is empty or the string is shorter than 6 chars while the
	 * input string is longer than 12 chars, the string is replaced with a md5 hash shortened to 16 chars.
	 *
	 * @param string $str
	 * @param bool $removeDots
	 * @param int $maxChars
	 * @return string the slug
	 */
	public static function slug(string $str, $removeDots = false, $maxChars = 100) {
		if (strlen($str) === 0) {
			return '';
		}

		$slug = self::sanitize($str, $removeDots, $maxChars);

		if (strlen($slug) === 0 || (strlen($slug) < 6 && strlen($str) > 12)) {
			$slug = substr(md5($str), 0, 16);
		}

		return $slug;
	}

	/**
	 * Strip substring from end of string.
	 *
	 * @param string $str
	 * @param string $end
	 * @return string The processed string
	 */
	public static function stripEnd(string $str, string $end = '') {
		return preg_replace('/' . preg_quote($end, '/') . '$/', '', $str);
	}

	/**
	 * Strip substring from start of string.
	 *
	 * @param string $str
	 * @param string $start
	 * @return string The processed string
	 */
	public static function stripStart(string $str, string $start = '') {
		return preg_replace('/^' . preg_quote($start, '/') . '/', '', $str);
	}

	/**
	 * Removes all HTML and Markdown (!) tags.
	 *
	 * @param string $str
	 * @return string The clean string
	 */
	public static function stripTags(string $str) {
		return trim(strip_tags(Str::markdown(strip_tags($str))));
	}
}
