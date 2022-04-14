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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine\Processors;

use Automad\Engine\PatternAssembly;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The URL processor class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class URLProcessor {
	/**
	 * Find and resolve URLs using the specified resolving method and parameters.
	 *
	 * @param string $str
	 * @param string $method
	 * @param array $parameters
	 * @return string the processed string
	 */
	public static function resolveUrls(string $str, string $method, array $parameters = array()) {
		$method = '\Automad\Core\Resolve::' . $method;

		// Find URLs in markdown like ![...](image.jpg?100x100).
		$str = preg_replace_callback(
			'/(\!\[[^\]]*\]\()([^\)]+\.(?:jpg|jpeg|gif|png))([^\)]*\))/is',
			function ($match) use ($method, $parameters) {
				$parameters = array_merge(array(0 => $match[2]), $parameters);
				$url = call_user_func_array($method, $parameters);

				if (file_exists(AM_BASE_DIR . $url)) {
					return $match[1] . $url . $match[3];
				} else {
					return $match[0];
				}
			},
			$str
		);

		// Remove all temporary edit buttons inside HTML tags to avoid confusing the URL resolver.
		$str = preg_replace_callback(
			'/<([^>]+)>/s',
			function ($match) {
				return '<' . preg_replace('/' . PatternAssembly::inPageEditButton() . '/s', '', $match[1]) . '>';
			},
			$str
		);

		// Find URLs in action, href and src attributes.
		// Note that all URLs in markdown code blocks will be ignored (<[^>]+).
		$str = preg_replace_callback(
			'/(<[^>]+(?:action|href|src))=((?:\\\\)?")(.+?)((?:\\\\)?")/is',
			function ($match) use ($method, $parameters) {
				$parameters = array_merge(array(0 => $match[3]), $parameters);
				$url = call_user_func_array($method, $parameters);
				// Matches 2 and 4 are quotes.
				return $match[1] . '=' . $match[2] . $url . $match[4];
			},
			$str
		);

		// Inline styles (like background-image).
		// Note that all URLs in markdown code blocks will be ignored (<[^>]+).
		$str = preg_replace_callback(
			'/(<[^>]+)url\(\'?(.+?)\'?\)/is',
			function ($match) use ($method, $parameters) {
				$parameters = array_merge(array(0 => $match[2]), $parameters);
				$url = call_user_func_array($method, $parameters);

				return $match[1] . 'url(\'' . $url . '\')';
			},
			$str
		);

		// Image srcset attributes.
		// Note that all URLs in markdown code blocks will be ignored (<[^>]+).
		$str = preg_replace_callback(
			'/(<[^>]+srcset)=((?:\\\\)?")([^"]+)((?:\\\\)?")/is',
			function ($match) use ($method, $parameters) {
				$urls = preg_replace_callback(
					'/([^,\s]+)\s+(\w+)/is',
					function ($match) use ($method, $parameters) {
						$parameters = array_merge(array(0 => $match[1]), $parameters);

						return call_user_func_array($method, $parameters) . ' ' . $match[2];
					},
					$match[3]
				);
				// Matches 2 and 4 are quotes.
				return $match[1] . '=' . $match[2] . $urls . $match[4];
			},
			$str
		);

		return $str;
	}
}
