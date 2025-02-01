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
 * Copyright (c) 2018-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The SessionData class handles setting and getting items of $_SESSION[Session::DATA_KEY].
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2018-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SessionData {
	/**
	 * Get the session data array or just one value in case $key is defined.
	 *
	 * @param string|null $key
	 * @return mixed The data array or a single value
	 */
	public static function get(?string $key = null): mixed {
		if (!isset($_SESSION[Session::DATA_KEY])) {
			$_SESSION[Session::DATA_KEY] = array();
		}

		if ($key) {
			return $_SESSION[Session::DATA_KEY][$key] ?? '';
		}

		return $_SESSION[Session::DATA_KEY];
	}

	/**
	 * Set a key/value pair in the session data array.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public static function set(string $key, $value): void {
		if (!isset($_SESSION[Session::DATA_KEY])) {
			$_SESSION[Session::DATA_KEY] = array();
		}

		$_SESSION[Session::DATA_KEY][$key] = $value;
	}
}
