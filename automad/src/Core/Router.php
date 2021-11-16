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

namespace Automad\Core;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Router class handles the registration and evaluation of routes.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Router {
	/**
	 * The routes array.
	 */
	private $routes = array();

	/**
	 * The constructor.
	 */
	public function __construct() {
		Debug::log('Created new Router');
	}

	/**
	 * Test a given URL against the registered route patterns and return a matching function.
	 * Note that the routes are testes in order they are stored in the routes array. Therefore
	 * it is important that the most generic routes are registered last.
	 *
	 * @param string $url
	 */
	public function get(string $url) {
		foreach ($this->routes as $route => $callable) {
			if (preg_match("#^$route$#i", $url)) {
				Debug::log($route, $url);

				return $callable;
			}
		}

		return function () {};
	}

	/**
	 * Register a new route.
	 *
	 * @param string $route
	 * @param callable $callable
	 */
	public function register(string $route, callable $callable) {
		$this->routes[$route] = $callable;
	}
}
