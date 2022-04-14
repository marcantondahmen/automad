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

namespace Automad\Engine\Collections;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The snippet collection.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SnippetCollection {
	/**
	 * An array for collection snippet definitions.
	 */
	private static $snippets = array();

	/**
	 * Add a snippet definition to the collection.
	 *
	 * @param string $name
	 * @param string $body
	 */
	public static function add(string $name, string $body) {
		self::$snippets[$name] = $body;
	}

	/**
	 * Get a snippet definition body.
	 *
	 * @param string $name
	 * @return string the snippet body
	 */
	public static function get(string $name) {
		if (array_key_exists($name, self::$snippets)) {
			return self::$snippets[$name];
		}

		return '';
	}

	/**
	 * Get the full collection of snippet definitions.
	 *
	 * @return array the array of snippet definitions.
	 */
	public static function getCollection() {
		return self::$snippets;
	}
}
