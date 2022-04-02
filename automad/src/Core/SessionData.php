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
 * Copyright (c) 2018-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The SessionData class handles setting and getting items of $_SESSION['data'].
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2018-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SessionData {
	/**
	 * Get the session data array or just one value in case $key is defined.
	 *
	 * @param string|null $key
	 * @return mixed The data array or a single value
	 */
	public static function get(?string $key = null) {
		if (!isset($_SESSION['data'])) {
			$_SESSION['data'] = array();
		}

		if ($key) {
			if (array_key_exists($key, $_SESSION['data'])) {
				return $_SESSION['data'][$key];
			} else {
				return '';
			}
		} else {
			return $_SESSION['data'];
		}
	}

	/**
	 * Set a key/value pair in the session data array.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public static function set(string $key, $value) {
		if (!isset($_SESSION['data'])) {
			$_SESSION['data'] = array();
		}

		$_SESSION['data'][$key] = $value;
	}
}
