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

namespace Automad;

use Automad\Core\Config;
use Automad\Core\Debug;
use Automad\Core\Error;
use Automad\Core\FileSystem;
use Automad\Core\Request;
use Automad\Core\Router;
use Automad\Engine\Document\Body;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The App class instance takes care of running all required startup tests,
 * initializing PHP sessions, setting up the autoloader, reading the configuration
 * and displaying the final output for a request.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class App {
	const VERSION = '2.0.0-beta.4';

	/**
	 * Required PHP version.
	 */
	private string $requiredPhpVersion = '8.2.0';

	/**
	 * The main app constructor takes care of running all required startup tests,
	 * initializing PHP sessions, setting up the autoloader, reading the configuration
	 * and displaying the final output for a request.
	 */
	public function __construct() {
		define('AM_VERSION', App::VERSION);

		require_once __DIR__ . '/Core/FileSystem.php';
		define('AM_BASE_DIR', FileSystem::normalizeSlashes(dirname(dirname(dirname(__DIR__)))));

		require_once __DIR__ . '/Core/Error.php';
		Error::setHtmlOutputHandler();

		$this->runVersionCheck();

		require_once __DIR__ . '/Autoload.php';
		Autoload::init();

		date_default_timezone_set(@date_default_timezone_get());

		Config::init();
		Debug::errorReporting();

		$this->setOpenBaseDir();

		define('AM_REQUEST', Request::page());

		$this->runPermissionCheck();
		$this->startSession();

		$output = $this->render(AM_REQUEST);

		if (AM_DEBUG_ENABLED) {
			Debug::json();
			$output = Body::append($output, Debug::consoleLog());
		}

		exit($output);
	}

	/**
	 * Get the generated output for the current request.
	 *
	 * @param string $request
	 * @return string the rendered output
	 */
	private function render(string $request): string {
		$Router = new Router();
		Routes::init($Router);
		$callable = $Router->get($request);

		return $callable();
	}

	/**
	 * Run a basic permission check on the cache directory.
	 */
	private function runPermissionCheck(): void {
		if (!is_writable(AM_BASE_DIR . AM_DIR_CACHE)) {
			Error::exit('Permission denied', 'The "' . AM_DIR_CACHE . '" directory must be writable by the web server.');
		}
	}

	/**
	 * Run a basic PHP version check.
	 */
	private function runVersionCheck(): void {
		if (version_compare(PHP_VERSION, $this->requiredPhpVersion, '<')) {
			Error::exit('PHP out of date', "Please update your PHP version to $this->requiredPhpVersion or newer.");
		}
	}

	/**
	 * Enable basedir restriction if configured.
	 */
	private function setOpenBaseDir(): void {
		if (AM_OPEN_BASEDIR_ENABLED) {
			ini_set(
				'open_basedir',
				AM_BASE_DIR . PATH_SEPARATOR . AM_DIR_TMP . PATH_SEPARATOR . sys_get_temp_dir()
			);
		}
	}

	/**
	 * Initialize a PHP session.
	 */
	private function startSession(): void {
		session_name('Automad-' . md5(AM_BASE_DIR));
		session_set_cookie_params(0, AM_BASE_URL ?: '/', '', false, true);
		session_start();
	}
}
