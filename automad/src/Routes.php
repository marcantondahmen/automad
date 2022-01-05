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

use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\Feed;
use Automad\Core\Parse;
use Automad\Core\Router;
use Automad\Core\Str;
use Automad\Engine\View;
use Automad\UI\Bootstrap;
use Automad\UI\Dashboard;
use Automad\UI\Response;
use Automad\UI\Utils\Session;

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
		$Router->register(
			AM_PAGE_DASHBOARD . '/bootstrap.js',
			function () {
				return Bootstrap::file();
			},
			AM_PAGE_DASHBOARD
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '/api/.*',
			function () {
				$apiRoute = trim(Str::stripStart(AM_REQUEST, AM_PAGE_DASHBOARD . '/api'), '/');

				return Dashboard::api($apiRoute);
			},
			AM_PAGE_DASHBOARD && Session::getUsername()
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '/api/.*',
			function () {
				header('Content-Type: application/json; charset=utf-8');

				$Response = new Response();
				$Response->setRedirect(AM_BASE_INDEX . AM_PAGE_DASHBOARD);

				exit($Response->json());
			},
			AM_PAGE_DASHBOARD
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '/ResetPassword',
			function () {
				return Dashboard::view('ResetPassword');
			},
			AM_PAGE_DASHBOARD && file_exists(AM_FILE_ACCOUNTS)
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '(/.*)?',
			function () {
				$view = trim(Str::stripStart(AM_REQUEST, AM_PAGE_DASHBOARD), '/');

				return Dashboard::view($view);
			},
			AM_PAGE_DASHBOARD && Session::getUsername()
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '(/.*)?',
			function () {
				return Dashboard::view('Login');
			},
			AM_PAGE_DASHBOARD && file_exists(AM_FILE_ACCOUNTS)
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '(/.*)?',
			function () {
				return Dashboard::view('CreateUser');
			},
			AM_PAGE_DASHBOARD
		);

		$Router->register(AM_FEED_URL, function () {
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
		}, AM_FEED_ENABLED);

		$Router->register('/.*', function () {
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
		});

		self::$registered = $Router->getRoutes();
	}
}
