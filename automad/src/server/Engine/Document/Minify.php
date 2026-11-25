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
 * Copyright (c) 2025-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Engine\Document;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The minify helper class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Minify {
	/**
	 * Minify custom CSS.
	 *
	 * @param string $css
	 * @return string
	 */
	public static function css(string $css): string {
		return trim(
			/** @var string */
			preg_replace(
				'/(\s(?=\s)|(?<=})\s|(?<={)\s|\s(?={)|\s(?=})|(?<=;)\s|\s(?=;)|(?<=:)\s|(?<=,)\s)/s',
				'',
				preg_replace(
					'/\s+/s',
					' ',
					preg_replace('/\/\*.*?\*\//s', '', $css) ?? ''
				) ?? ''
			) ?? ''
		);
	}

	/**
	 * Minify HTML.
	 *
	 * - Leave <pre>, <textarea>, <script> and <style> blocks untouched.
	 * - Remove HTML comments.
	 * - Collapse whitespace next to tags into a single space, so rendering
	 *   is unchanged. Attribute values and tag interiors are never modified.
	 *
	 * @param string $html
	 * @return string
	 */
	public static function html(string $html): string {
		$protected = array();

		// Stash whitespace-sensitive blocks behind tag-like placeholders.
		$html = preg_replace_callback(
			'#<(pre|textarea|script|style)\b[^>]*>.*?</\1>#is',
			function (array $m) use (&$protected): string {
				$key = "<\x00" . count($protected) . "\x00>";
				$protected[$key] = $m[0];

				return $key;
			},
			$html
		) ?? '';

		$html = preg_replace('#<!--.*?-->#s', '', $html) ?? '';

		// Collapse whitespace inside opening tags, but never inside quoted values.
		$html = preg_replace_callback(
			'#<[a-zA-Z][^\s/>]*+(?:"[^"]*+"|\'[^\']*+\'|[^>"\']++)*+>#',
			function (array $tag): string {
				$out = preg_replace_callback(
					'#("[^"]*+"|\'[^\']*+\')|[ \t\n\f\r]+#',
					fn (array $m): string => $m[1] ?? ' ',
					$tag[0]
				) ?? '';

				// Remove the space before `>` or `/>` after a quoted value only:
				// `<a href="x" >` -> `<a href="x">`, `<img src="x" />` -> `<img src="x"/>`
				return preg_replace('#(["\']) (/?>)$#', '$1$2', $out) ?? '';
			},
			$html
		) ?? '';

		// Collapse whitespace adjacent to tags into a single space.
		// Opening tags are matched as a whole and returned unchanged,
		// so whitespace inside quoted attribute values is never touched.
		$html = preg_replace_callback(
			'#<[a-zA-Z][^\s/>]*+(?:"[^"]*+"|\'[^\']*+\'|[^>"\']++)*+>|(?<=>)[ \t\n\f\r]+|[ \t\n\f\r]+(?=<)#',
			fn (array $m): string => $m[0][0] === '<' ? $m[0] : ' ',
			$html
		) ?? '';

		return trim(strtr($html, $protected));
	}
}
