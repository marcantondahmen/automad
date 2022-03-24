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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad;

use Automad\Admin\API\RequestHandler;
use Automad\Admin\API\Response;
use Automad\Admin\Session;
use Automad\Admin\UI\Dashboard;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\Feed;
use Automad\Core\Parse;
use Automad\Core\Router;
use Automad\Engine\View;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Routes class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Routes {
	/**
	 * An array of reserved routes that can't be used by any page.
	 */
	public static $registered = array();

	/**
	 * Register routes to a giver Router.
	 *
	 * @param Router $Router
	 */
	public static function init(Router $Router) {
		$isLoggedIn = AM_PAGE_DASHBOARD && Session::getUsername();
		$apiBase = RequestHandler::$apiBase;

		// API
		$Router->register(
			"$apiBase/.*",
			function () {
				return RequestHandler::getResponse();
			},
			$isLoggedIn
		);

		$Router->register(
			"$apiBase/(Session/login|App/(bootstrap|updateState))",
			function () {
				return RequestHandler::getResponse();
			},
			AM_PAGE_DASHBOARD
		);

		$Router->register(
			"$apiBase/.*",
			function () {
				header('Content-Type: application/json; charset=utf-8');

				$Response = new Response();
				$Response->setRedirect('/login');

				exit($Response->json());
			},
			AM_PAGE_DASHBOARD
		);

		// Dashboard
		$Router->register(
			AM_PAGE_DASHBOARD . '/setup',
			function () {
				self::redirectDashboard('/login');
			},
			is_readable(AM_FILE_ACCOUNTS)
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '/setup',
			function () {
				return Dashboard::render();
			},
			!is_readable(AM_FILE_ACCOUNTS)
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '(/.*)?',
			function () {
				self::redirectDashboard('/setup');
			},
			!is_readable(AM_FILE_ACCOUNTS)
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '/(login|resetpassword|logout)',
			function () {
				self::redirectDashboard('/home');
			},
			$isLoggedIn
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '(/.*)?',
			function () {
				return Dashboard::render();
			},
			$isLoggedIn
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '/(login|resetpassword|logout)',
			function () {
				return Dashboard::render();
			},
			AM_PAGE_DASHBOARD
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '(/.*)?',
			function () {
				self::redirectDashboard('/login');
			},
			AM_PAGE_DASHBOARD
		);

		// Feed
		$Router->register(
			AM_FEED_URL,
			function () {
				header('Content-Type: application/rss+xml; charset=UTF-8');

				$Cache = new Cache();

				if ($Cache->pageCacheIsApproved()) {
					return $Cache->readPageFromCache();
				}

				$Feed = new Feed(
					$Cache->getAutomad(),
					Parse::csv(AM_FEED_FIELDS)
				);

				return $Feed->get();
			},
			AM_FEED_ENABLED
		);

		// Pages
		$Router->register(
			'/.*',
			function () {
				if (AM_HEADLESS_ENABLED) {
					header('Content-Type: application/json; charset=utf-8');
				}

				$Cache = new Cache();

				if ($Cache->pageCacheIsApproved()) {
					return $Cache->readPageFromCache();
				}

				$Automad = $Cache->getAutomad($Cache);
				$View = new View($Automad, AM_HEADLESS_ENABLED);
				$output = $View->render();

				if ($Automad->currentPageExists()) {
					$Cache->writePageToCache($output);
				} else {
					Debug::log(AM_REQUEST, 'Page not found! Caching will be skipped!');
				}

				return $output;
			}
		);

		self::$registered = $Router->getRoutes();
	}

	/**
	 * Redirect to a given route
	 *
	 * @param string $route
	 */
	private static function redirectDashboard(string $route) {
		header('Location: ' . AM_BASE_INDEX . AM_PAGE_DASHBOARD . $route, true, 301);
		exit();
	}
}
