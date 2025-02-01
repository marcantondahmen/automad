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

namespace Automad\Core;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Router class handles the registration and evaluation of routes.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Router {
	/**
	 * The routes array.
	 */
	private array $routes = array();

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
	 * @return callable
	 */
	public function get(string $url): callable {
		foreach ($this->routes as $item) {
			$route = $item['route'];
			$callable = $item['callable'];

			if (preg_match("#^$route$#i", $url)) {
				Debug::log($route, $url);

				return $callable;
			}
		}

		return function () {};
	}

	/**
	 * Get the routes array.
	 *
	 * @return array the array of routes
	 */
	public function getRoutes(): array {
		return $this->routes;
	}

	/**
	 * Register a new route in case $condition is true.
	 *
	 * @param string $route
	 * @param callable $callable
	 * @param mixed $condition
	 */
	public function register(string $route, callable $callable, $condition = true): void {
		if ($condition && $route) {
			$this->routes[] = array(
				'route' => $route,
				'callable' => $callable
			);
		}
	}
}
