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
 * Copyright (c) 2021-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Engine\Collections;

use Automad\Engine\Snippet;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The snippet collection.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class SnippetCollection {
	/**
	 * An array for collection snippet definitions.
	 *
	 * @var Snippet[]
	 */
	private static array $snippets = array();

	/**
	 * Add a snippet to the collection.
	 *
	 * @param string $name
	 * @param string $body
	 * @param string $path
	 */
	public static function add(string $name, string $body, string $path): void {
		self::$snippets[$name] = new Snippet($body, $path);
	}

	/**
	 * Get a snippet.
	 *
	 * @param string $name
	 * @return Snippet|null the snippet
	 */
	public static function get(string $name): Snippet|null {
		if (array_key_exists($name, self::$snippets)) {
			return self::$snippets[$name];
		}

		return null;
	}

	/**
	 * Get the full collection of snippets.
	 *
	 * @return Snippet[] the array of snippets.
	 */
	public static function getCollection(): array {
		return self::$snippets;
	}
}
