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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\System;

use Automad\Core\Debug;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Server util class contains helper functions for server information.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Server {
	/**
	 * Get the base URL of the current installation.
	 * In case the site is behind a proxy, set AM_BASE_URL to AM_BASE_PROXY.
	 * AM_BASE_PROXY can be separately defined to enable running a site with and without a proxy in parallel.
	 *
	 * In case the site is not running behind a proxy server, just get the base URL from the script name.
	 *
	 * @return string
	 */
	public static function getBaseUrl(): string {
		if (Server::isForwarded()) {
			return '';
		}

		return str_replace('/index.php', '', $_SERVER['SCRIPT_NAME'] ?? '');
	}

	/**
	 * Get the server URL.
	 *
	 * @return string
	 */
	public static function getHost(): string {
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
		$host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? ($_SERVER['HTTP_HOST'] ?? '');

		return $protocol . '://' . $host;
	}

	/**
	 * Return the base url behind a proxy in case a request is forwarded.
	 *
	 * @return string
	 */
	public static function getProxyBaseUrl(): string {
		if (Server::isForwarded()) {
			return str_replace('/index.php', '', $_SERVER['SCRIPT_NAME'] ?? '');
		}

		return '';
	}

	/**
	 * Check whether a request is forwarded.
	 *
	 * @return bool
	 */
	public static function isForwarded(): bool {
		$proxyProps = array(
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED_PROTO',
			'HTTP_X_FORWARDED_HOST',
			'HTTP_X_FORWARDED_SERVER'
		);

		foreach ($proxyProps as $key) {
			if (!empty($_SERVER[$key])) {
				Debug::log($_SERVER[$key], $key);

				return true;
			}
		}

		return false;
	}
}
