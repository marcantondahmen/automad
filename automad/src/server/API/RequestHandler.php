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
 * Copyright (c) 2014-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\API;

use Automad\Core\Debug;
use Automad\Core\Messenger;
use Automad\Core\Request;
use Automad\Core\Session;
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The API class handles all user interactions using the dashboard.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2014-2022 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class RequestHandler {
	/**
	 * The API base route.
	 */
	public static string $apiBase = '/api';

	/**
	 * The controller namespace.
	 */
	private static string $controllerNamespace = '\\Automad\\Controllers\\API\\';

	/**
	 * An array of routes that are excluded from CSRF token validation.
	 */
	private static array $validationExcluded = array(
		'Session/login',
		'User/resetPassword'
	);

	/**
	 * Get the JSON response for a requested route
	 *
	 * @return string the JSON formatted response
	 */
	public static function getResponse(): string {
		header('Content-Type: application/json; charset=utf-8');

		$apiRoute = trim(Str::stripStart(AM_REQUEST, self::$apiBase), '/');

		Debug::log($apiRoute);

		$method = self::$controllerNamespace . str_replace('/', 'Controller::', $apiRoute);
		$parts = explode('::', $method);
		$class = $parts[0];

		if (!empty($parts[1]) && self::classFileExists($class) && method_exists($class, $parts[1])) {
			$Messenger = new Messenger();

			if (self::validate($apiRoute, $Messenger)) {
				self::registerControllerErrorHandler();
				$Response = call_user_func($method);
			} else {
				$Response = new Response();
				$Response->setCode(403);
				$Response->setError($Messenger->getError());
			}
		} else {
			$Response = new Response();
			$Response->setCode(404);
			$Response->setError('Invalid API route: ' . $apiRoute);
		}

		$Response->setDebug(Debug::getLog());

		return $Response->json();
	}

	/**
	 * Test whether a file of a given class is readable.
	 *
	 * @param string $className
	 * @return bool true in case the file is readable.
	 */
	private static function classFileExists(string $className): bool {
		$prefix = 'Automad\\';
		$file = AM_BASE_DIR . '/automad/src/server/' . str_replace('\\', '/', substr($className, strlen($prefix))) . '.php';

		return is_readable($file);
	}

	/**
	 * Register a error handler that sends a 500 response code in case of a fatal error created by a controller.
	 */
	private static function registerControllerErrorHandler(): void {
		error_reporting(0);

		register_shutdown_function(function () {
			$error = error_get_last();

			if (is_array($error) && !empty($error['type']) && $error['type'] === 1) {
				http_response_code(500);
				exit(json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
			}
		});
	}

	/**
	 * Validate request by checking the CSRF token in case of a post request.
	 *
	 * @param string $route
	 * @param Messenger $Messenger
	 * @return bool true if the request is valid
	 */
	private static function validate(string $route, Messenger $Messenger): bool {
		if (in_array($route, self::$validationExcluded)) {
			return true;
		}

		if (!empty($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$token = Request::post('__csrf__');

			if (empty($token) || !Session::verifyCsrfToken($token)) {
				$Messenger->setError('CSRF token mismatch');

				return false;
			}
		}

		return true;
	}
}
