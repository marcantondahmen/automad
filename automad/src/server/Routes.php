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
use Automad\Controllers\PageController;
use Automad\Core\Cache;
use Automad\Core\Feed;
use Automad\Core\Parse;
use Automad\Core\Router;

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
		$isAuthenticatedUser = AM_PAGE_DASHBOARD && Session::getUsername();

		self::registerAPIRoutes($Router, $isAuthenticatedUser);
		self::registerDashboardRoutes($Router, $isAuthenticatedUser);
		self::registerFeedRoute($Router);
		self::registerPageRoutes($Router);

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

	/**
	 * Register API routes.
	 *
	 * @param Router $Router
	 * @param bool $isAuthenticatedUser
	 */
	private static function registerAPIRoutes(Router $Router, bool $isAuthenticatedUser) {
		$apiBase = RequestHandler::$apiBase;

		$Router->register(
			"$apiBase/.*",
			function () {
				return RequestHandler::getResponse();
			},
			$isAuthenticatedUser
		);

		$Router->register(
			"$apiBase/(Session/login|Session/validate|App/bootstrap|User/resetPassword)",
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
				$Response->setRedirect('login');

				exit($Response->json());
			},
			AM_PAGE_DASHBOARD
		);
	}

	/**
	 * Register dashboard routes.
	 *
	 * @param Router $Router
	 * @param bool $isAuthenticatedUser
	 */
	private static function registerDashboardRoutes(Router $Router, bool $isAuthenticatedUser) {
		$hasAccounts = is_readable(AM_FILE_ACCOUNTS);

		$Router->register(
			AM_PAGE_DASHBOARD . '/setup',
			function () {
				return Dashboard::render();
			},
			!$hasAccounts
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '(/.*)?',
			function () {
				self::redirectDashboard('/setup');
			},
			!$hasAccounts
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '/setup',
			function () {
				self::redirectDashboard('/login');
			},
			$hasAccounts
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '/(login|resetpassword)',
			function () {
				self::redirectDashboard('/home');
			},
			$isAuthenticatedUser
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '/(login|resetpassword)',
			function () {
				return Dashboard::render();
			},
			AM_PAGE_DASHBOARD
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '(/.*)?',
			function () {
				return Dashboard::render();
			},
			$isAuthenticatedUser
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '(/.*)?',
			function () {
				self::redirectDashboard('/login');
			},
			AM_PAGE_DASHBOARD
		);
	}

	/**
	 * Register the RSS feed route.
	 *
	 * @param Router $Router
	 */
	private static function registerFeedRoute(Router $Router) {
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
	}

	/**
	 * Register all left-over routes as page routes.
	 *
	 * @param Router $Router
	 */
	private static function registerPageRoutes(Router $Router) {
		$Router->register(
			'/.*',
			array(PageController::class, 'render')
		);
	}
}
