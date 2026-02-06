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
 * Copyright (c) 2024-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Engine\Document;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The head helper class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2024-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Head {
	/**
	 * Append a tag to the <head> element.
	 *
	 * @param string $doc
	 * @param string $tag
	 * @return string
	 */
	public static function append(string $doc, string $tag): string {
		$split = preg_split('#(</head>\s*(?:<!--.*?-->)*\s*<body[^>]*>)#is', $doc, 2, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		if (empty($split[0]) || empty($split[1])) {
			return $doc;
		}

		return $split[0] . $tag . $split[1] . $split[2];
	}

	/**
	 * Prepend a tag to the <head> element.
	 *
	 * @param string $doc
	 * @param string $tag
	 * @return string
	 */
	public static function prepend(string $doc, string $tag): string {
		$split = preg_split('#^((?:\s*<[^>]*>)*\s*<html[^>]*>\s*(?:<!--.*?-->)*\s*<head>)#is', $doc, 2, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		if (empty($split[0]) || empty($split[1])) {
			return $doc;
		}

		return $split[0] . $tag . $split[1];
	}
}
