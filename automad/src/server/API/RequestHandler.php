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
 * Copyright (c) 2014-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\API;

use Automad\Core\Debug;
use Automad\Core\Error;
use Automad\Core\Messenger;
use Automad\Core\Request;
use Automad\Core\Session;
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The API class handles all user interactions using the dashboard.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2014-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class RequestHandler {
	const API_BASE = '/_api';
	const REQUEST_KEY_CSRF = '__csrf__';
	const REQUEST_KEY_JSON = '__json__';

	/**
	 * The controller namespace.
	 */
	private static string $controllerNamespace = '\\Automad\\Controllers\\API\\';

	/**
	 * An array of routes that are excluded from CSRF token validation.
	 */
	private static array $validationExcluded = array(
		'SessionController::login',
		'UserController::resetPassword'
	);

	/**
	 * Get the JSON response for a requested route
	 *
	 * @return string the JSON formatted response
	 */
	public static function getResponse(): string {
		Error::setJsonResponseHandler();

		header('Content-Type: application/json; charset=utf-8');

		$controller = self::routeController(AM_REQUEST);

		[$class, $method] = explode('::', $controller);

		Debug::log($controller, AM_REQUEST);

		if (self::classFileExists($class) && method_exists($class, $method)) {
			$Messenger = new Messenger();

			if (self::validate($controller, $Messenger)) {
				self::convertJsonPost();
				$Response = call_user_func($controller);
			} else {
				$Response = new Response();
				$Response->setCode(403);
				$Response->setError($Messenger->getError());
			}
		} else {
			$Response = new Response();
			$Response->setCode(404);
			$Response->setError('Invalid API route: ' . AM_REQUEST . ' [' . $controller . ']');
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
	 * Parse __json__ field and merged the parsed data back to $_POST.
	 */
	private static function convertJsonPost(): void {
		$json = $_POST[self::REQUEST_KEY_JSON] ?? null;

		if (is_string($json)) {
			$_POST = array_merge($_POST, json_decode($json, true));
			unset($_POST[self::REQUEST_KEY_JSON]);
		}
	}

	/**
	 * Convert a route into a controller name.
	 *
	 * @param string $route
	 * @return string the controller name
	 */
	private static function routeController(string $route): string {
		$route = str_replace(self::API_BASE . '/', '', $route);
		[$class, $method] = explode('/', $route);

		$class = self::$controllerNamespace . str_replace(' ', '', ucwords(str_replace('-', ' ', $class))) . 'Controller';
		$method = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $method))));

		return "$class::$method";
	}

	/**
	 * Validate request by checking the CSRF token in case of a post request.
	 *
	 * @param string $controller
	 * @param Messenger $Messenger
	 * @return bool true if the request is valid
	 */
	private static function validate(string $controller, Messenger $Messenger): bool {
		if (in_array(Str::stripStart($controller, self::$controllerNamespace), self::$validationExcluded)) {
			return true;
		}

		if (!empty($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$token = Request::post(self::REQUEST_KEY_CSRF);

			if (empty($token) || !Session::verifyCsrfToken($token)) {
				$Messenger->setError('CSRF token mismatch');

				return false;
			}
		}

		return true;
	}
}
