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
 * Copyright (c) 2014-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI;

use Automad\Core\Debug;
use Automad\Core\Request;
use Automad\UI\Utils\Prefix;
use Automad\UI\Utils\Session;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The dashboard class handles all user interactions using the dashboard.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2014-2021 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Dashboard {
	/**
	 * The dashboard output.
	 */
	private $output;

	/**
	 * The dashboard constructor.
	 */
	public function __construct() {
		// Load text modules.
		Text::parseModules();

		$namespaceViews = __NAMESPACE__ . '\\Views\\';
		$namespaceControllers = __NAMESPACE__ . '\\Controllers\\';

		if (Session::getUsername()) {
			if ($controller = Request::query('controller')) {
				// Controllers.
				$method = "{$namespaceControllers}{$controller}";
				$method = str_replace('::', 'Controller::', $method);
				$parts = explode('::', $method);
				$class = $parts[0];

				header('Content-Type: application/json; charset=utf-8');

				if (!empty($parts[1]) && $this->classFileExists($class) && method_exists($class, $parts[1])) {
					$this->registerControllerErrorHandler();
					$Response = call_user_func($method);
					$Response->setDebug(Debug::getLog());
				} else {
					http_response_code(404);
					$Response = new Response();
				}

				$this->output = $Response->json();
			} else {
				// Views.
				$default = 'Home';
				$view = Request::query('view');

				if (!$view) {
					$view = $default;
				}

				$class = "{$namespaceViews}{$view}";

				if (!$this->classFileExists($class)) {
					$class = "{$namespaceViews}{$default}";
				}

				$object = new $class;
				$this->output = $object->render();
			}
		} else {
			// In case a controller is requested without being authenticated, redirect page to login page.
			if (Request::query('controller')) {
				header('Content-Type: application/json; charset=utf-8');

				$Response = new Response();
				$Response->setRedirect(AM_BASE_INDEX . AM_PAGE_DASHBOARD);

				exit($Response->json());
			}

			$requestedView = Request::query('view');

			if ($requestedView == 'ResetPassword') {
				$view = $requestedView;
			} else {
				$view = 'Login';
			}

			if (!file_exists(AM_FILE_ACCOUNTS)) {
				$view = 'CreateUser';
			}

			$class = "{$namespaceViews}{$view}";
			$object = new $class;
			$this->output = $object->render();
		}
	}

	/**
	 * Get the rendered output.
	 *
	 * @return string the rendered output.
	 */
	public function get() {
		return Prefix::tags($this->output);
	}

	/**
	 * Test whether a file of a given class is readable.
	 *
	 * @param string $className
	 * @return bool true in case the file is readable.
	 */
	private function classFileExists(string $className) {
		$prefix = 'Automad\\';
		$file = AM_BASE_DIR . '/automad/src/' . str_replace('\\', '/', substr($className, strlen($prefix))) . '.php';

		return is_readable($file);
	}

	/**
	 * Register a error handler that sends a 500 response code in case of a fatal error created by a controller.
	 */
	private function registerControllerErrorHandler() {
		error_reporting(0);

		register_shutdown_function(function () {
			$error = error_get_last();

			if (is_array($error) && !empty($error['type']) && $error['type'] === 1) {
				http_response_code(500);
				exit(json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
			}
		});
	}
}
