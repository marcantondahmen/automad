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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The custom function wrapper class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class CustomFunction {
	/**
	 * The custom function registry.
	 */
	private static $registry = array();

	/**
	 * Register a custom function.
	 *
	 * @param string $name
	 * @param callable $func
	 */
	public static function add(string $name, callable $func): void {
		self::$registry[$name] = $func;
	}

	/**
	 * Call a custom function.
	 *
	 * @param string $name
	 * @param array $options
	 */
	public static function call(string $name, array $options): string {
		if (empty(self::$registry[$name])) {
			return '';
		}

		return self::$registry[$name]($options) ?? '';
	}

	/**
	 * Test if a function exists in the registry.
	 *
	 * @param string $name
	 */
	public static function exists(string $name): bool {
		return !empty(self::$registry[$name]);
	}
}
