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
use Automad\UI\Controllers\User;
use Automad\UI\Utils\Prefix;
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

		if (User::get()) {
			if ($controller = Request::query('controller')) {
				// Controllers.
				$method = "{$namespaceControllers}{$controller}";
				$parts = explode('::', $method);
				$class = $parts[0];

				header('Content-Type: application/json; charset=utf-8');

				if (!empty($parts[1]) && $this->classFileExists($class) && method_exists($class, $parts[1])) {
					$output = call_user_func($method);
					$output['debug'] = Debug::getLog();
				} else {
					header('HTTP/1.0 404 Not Found');
					$output = array();
				}

				$this->output = json_encode($output, JSON_UNESCAPED_SLASHES);
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
				die(json_encode(array('redirect' => AM_BASE_INDEX . AM_PAGE_DASHBOARD)));
			}

			$view = 'CreateUser';

			if (file_exists(AM_FILE_ACCOUNTS)) {
				$view = 'Login';
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
		$this->output = preg_replace('/^\t{0,3}/m', '', $this->output);

		return Prefix::tags($this->output);
	}

	/**
	 * Test whether a file of a given class is readable.
	 *
	 * @param mixed $className
	 * @return boolean true in case the file is readable.
	 */
	private function classFileExists($className) {
		return is_readable(
			AM_BASE_DIR . '/' . strtolower(str_replace('\\', '/', $className) . '.php')
		);
	}
}
