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

namespace Automad;

use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Core\Config;
use Automad\Core\Debug;
use Automad\Core\Feed;
use Automad\Core\FileSystem;
use Automad\Core\Parse;
use Automad\Core\Router;
use Automad\Core\Sitemap;
use Automad\Engine\View;
use Automad\UI\Dashboard;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The App class instance takes care of running all required startup tests,
 * initializing PHP sessions, setting up the autoloader, reading the configuration
 * and displaying the final output for a request.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class App {
	/**
	 * Required PHP version.
	 */
	private $requiredVersion = '7.4.0';

	/**
	 * The main app constructor takes care of running all required startup tests,
	 * initializing PHP sessions, setting up the autoloader, reading the configuration
	 * and displaying the final output for a request.
	 */
	public function __construct() {
		$this->runVersionCheck();
		date_default_timezone_set(@date_default_timezone_get());

		require_once __DIR__ . '/Core/FileSystem.php';
		define('AM_BASE_DIR', FileSystem::normalizeSlashes(dirname(dirname(__DIR__))));

		require_once __DIR__ . '/Autoload.php';
		Autoload::init();

		Config::overrides();
		Config::defaults();

		Debug::errorReporting();

		$this->runPermissionCheck();
		$this->startSession();

		$output = $this->render(AM_REQUEST);

		if (AM_DEBUG_ENABLED) {
			echo str_replace('</body>', Debug::consoleLog() . '</body>', $output);
		} else {
			echo $output;
		}
	}

	/**
	 * Get Automad from cache or create new instance.
	 *
	 * @param Cache $Cache
	 * @return Automad The Automad object
	 */
	private function getAutomad(Cache $Cache) {
		if ($Cache->automadObjectCacheIsApproved()) {
			$Automad = $Cache->readAutomadObjectFromCache();
		} else {
			$Automad = new Automad();
			$Cache->writeAutomadObjectToCache($Automad);
			new Sitemap($Automad->getCollection());
		}

		return $Automad;
	}

	/**
	 * Get the generated output for the current request.
	 *
	 * @param string $request
	 * @return string the rendered output
	 */
	private function render(string $request) {
		$Router = new Router();

		if (AM_PAGE_DASHBOARD) {
			$Router->register(AM_PAGE_DASHBOARD, function () {
				$Dashboard = new Dashboard();

				return $Dashboard->get();
			});
		}

		if (AM_FEED_ENABLED) {
			$Router->register(AM_FEED_URL, function () {
				header('Content-Type: application/rss+xml; charset=UTF-8');

				$Cache = new Cache();

				if ($Cache->pageCacheIsApproved()) {
					return $Cache->readPageFromCache();
				}

				$Feed = new Feed(
					$this->getAutomad($Cache),
					Parse::csv(AM_FEED_FIELDS)
				);

				return $Feed->get();
			});
		}

		$Router->register('/.*', function () use ($request) {
			if (AM_HEADLESS_ENABLED) {
				header('Content-Type: application/json; charset=utf-8');
			}

			$Cache = new Cache();

			if ($Cache->pageCacheIsApproved()) {
				return $Cache->readPageFromCache();
			}

			$Automad = $this->getAutomad($Cache);
			$View = new View($Automad, AM_HEADLESS_ENABLED);
			$output = $View->render();

			if ($Automad->currentPageExists()) {
				$Cache->writePageToCache($output);
			} else {
				Debug::log($request, 'Page not found! Caching will be skipped!');
			}

			return $output;
		});

		$callable = $Router->get($request);

		return $callable();
	}

	/**
	 * Run a basic permission check on the cache directory.
	 */
	private function runPermissionCheck() {
		if (!is_writable(AM_BASE_DIR . AM_DIR_CACHE)) {
			exit('<h1>Permission denied!</h1><h2>The "' . AM_DIR_CACHE . '" directory must be writable by the web server!</h2>');
		}
	}

	/**
	 * Run a basic PHP version check.
	 */
	private function runVersionCheck() {
		if (version_compare(PHP_VERSION, $this->requiredVersion, '<')) {
			exit("<h1>PHP out of date!</h1><h2>Please update your PHP version to $this->requiredVersion or newer!</h2>");
		}
	}

	/**
	 * Initialize a PHP session.
	 */
	private function startSession() {
		session_name('Automad-' . md5(AM_BASE_DIR));
		session_set_cookie_params(0, '/', '', false, true);
		session_start();
	}
}
