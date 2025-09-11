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
 * Copyright (c) 2016-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Carbon\Carbon;
use Cocur\Slugify\Slugify;
use Michelf\MarkdownExtra;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Str class holds all string methods.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Str {
	/**
	 * Change format of a given date, optionally according to locale settings.
	 *
	 * @see https://www.php.net/manual/en/datetime.format.php
	 * @param string $date
	 * @param string $format
	 * @param string|null $locale
	 * @return string The formatted date
	 */
	public static function dateFormat(string $date, string $format = 'D, d M Y', ?string $locale = null): string {
		if (!$date || !$format) {
			return '';
		}

		Carbon::setLocale($locale ?? 'en');

		$date = Carbon::parse($date);

		if (!$date) {
			return '';
		}

		return $date->translatedFormat($format);
	}

	/**
	 * Set a default value for $str in case $str is empty.
	 *
	 * @param string $str
	 * @param string $defaultValue
	 * @return string The default value
	 */
	public static function def(string $str, string $defaultValue = ''): string {
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
	public static function escape(string $str): string {
		// The json_encode() function is used to create a valid JSON string
		// with only one temporary key.
		// After getting that JSON string, the key, the brackets and quotes
		// are stripped to get the escaped version of $str.
		$str = preg_replace('/[\r\n]+/', '\n', trim($str));
		$str = json_encode(array('temp' => $str), JSON_UNESCAPED_SLASHES);
		$str = Str::stripStart($str ? $str : '', '{"temp":"');
		$str = Str::stripEnd($str, '"}');

		return $str;
	}

	/**
	 * Find the URL of the first image within rendered HTML markup.
	 *
	 * @param string $str
	 * @return string The URL of the first image or an empty string
	 */
	public static function findFirstImage(string $str): string {
		if (!$str) {
			return '';
		}

		preg_match('/<(?:img|am-img-loader|am-gallery|am-image-slideshow)[^>]+(?:first|src)="([^"]+)"/is', $str, $matches);

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
	public static function findFirstParagraph(string $str): string {
		if (!$str) {
			return '';
		}

		// First remove any paragraph only containing an image.
		$str = preg_replace('/<p>\s*<img.+?><\/p>/is', '', $str) ?? '';
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
	public static function markdown(string $str, $multilineOnly = false): string {
		// In case $str has no line breaks and $multilineOnly is enabled, skip parsing.
		if (strpos($str, "\n") === false && $multilineOnly) {
			return $str;
		}

		// Fix syntax highlighting.
		/** @var string */
		$str = preg_replace('/```(\w+)/is', '```language-$1', $str);

		// Fix strikethrough.
		/** @var string */
		$str = preg_replace('/~~([\w][^~]*[\w])~~/is', '<del>$1</del>', $str);

		$str = MarkdownExtra::defaultTransform($str);

		/** @var string */
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
	 * @return bool
	 */
	public static function match(string $str, string $regex = ''): bool {
		return (bool) preg_match($regex, $str);
	}

	/**
	 * Search and replace by regex.
	 *
	 * @param string $str
	 * @param string $regex
	 * @param string $replace
	 * @return string The processed string
	 */
	public static function replace(string $str, string $regex = '', string $replace = ''): string {
		return preg_replace($regex, $replace, $str) ?? '';
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
	public static function sanitize(string $str, $removeDots = false, $maxChars = 100): string {
		if (strlen($str) === 0) {
			return '';
		}

		$Slugify = new Slugify(
			array(
				'regexp' => $removeDots ? '/[^A-Za-z0-9]+/' : '/[^A-Za-z0-9\.]+/',
				'strip_tags' => true
			)
		);

		$Slugify->addRule('&', '-and-');
		$Slugify->addRule('+', '-plus-');
		$Slugify->addRule('@', '-at-');
		$Slugify->addRule('*', '-x-');
		$Slugify->addRule('&mdash;', '-');
		$Slugify->addRule('&ndash;', '-');

		$str = $Slugify->slugify($str);

		if (strlen($str) > $maxChars) {
			$str = substr($str, 0, $maxChars);
		}

		return trim($str, '-');
	}

	/**
	 * Shortens a string keeping full words. Note that this method also first strips all tags from the given string.
	 *
	 * @param string $str
	 * @param int $maxChars
	 * @param string $ellipsis
	 * @return string The shortened string
	 */
	public static function shorten(string $str, $maxChars, string $ellipsis = ' ...'): string {
		if (strlen($str) === 0) {
			return '';
		}

		$str = Str::stripTags($str);
		$str = preg_replace('/[\n\r]+/s', ' ', $str) ?? '';

		// Shorten $text to maximal characters (full words).
		if (strlen($str) > $maxChars) {
			// Cut $str to $maxChars +1.
			// Note that it has to be +1 to be able to catch the edge case,
			// where $maxChars is exactly an end of a word. +1 would than
			// be a space.
			$str = substr($str, 0, $maxChars + 1);

			// Find last space and get position.
			$pos = strrpos($str, ' ');

			// If there is no space left, use the original $maxChars (without +1) as $pos.
			if (!$pos) {
				$pos = $maxChars;
			}

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
	public static function slug(string $str, $removeDots = false, $maxChars = 100): string {
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
	public static function stripEnd(string $str, string $end = ''): string {
		return preg_replace('/' . preg_quote($end, '/') . '$/', '', $str) ?? '';
	}

	/**
	 * Strip substring from start of string.
	 *
	 * @param string $str
	 * @param string $start
	 * @return string The processed string
	 */
	public static function stripStart(string $str, string $start = ''): string {
		return preg_replace('/^' . preg_quote($start, '/') . '/', '', $str) ?? '';
	}

	/**
	 * Removes all HTML and Markdown (!) tags.
	 *
	 * @param string $str
	 * @return string The clean string
	 */
	public static function stripTags(string $str): string {
		return trim(strip_tags(Str::markdown(strip_tags($str))));
	}
}
