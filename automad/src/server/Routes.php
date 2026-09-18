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
 * Copyright (c) 2022-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad;

use Automad\Admin\Dashboard;
use Automad\Api\RequestHandler;
use Automad\Api\Response;
use Automad\Auth\Session\Session;
use Automad\Controllers\FeedController;
use Automad\Controllers\ImageController;
use Automad\Controllers\McpController;
use Automad\Controllers\PageController;
use Automad\Core\Debug;
use Automad\Core\Feed;
use Automad\Core\I18n;
use Automad\Core\Router;
use Automad\Models\UserCollection;
use Automad\System\FileSystem;
use Automad\System\SetupWizard;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Routes class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Routes {
	/**
	 * Public API routes.
	 */
	private static array $publicApiRoutes =array(
		'public/.*',
		'session/login',
		'session/validate',
		'app/bootstrap',
		'user/request-password-reset-code',
		'user/reset-password',
		'user-collection/create-first-user'
	);

	/**
	 * The array of registered routes.
	 */
	private static array $registered = array();

	/**
	 * The reserved routes that can't be used as page routes.
	 */
	private static array $reserved = array();

	/**
	 * Get the array of reserved routes.
	 *
	 * @return array
	 */
	public static function getReserved(): array {
		return self::$reserved;
	}

	/**
	 * Register routes to a giver Router.
	 *
	 * @param Router $Router
	 */
	public static function init(Router $Router): void {
		$isAuthenticatedUser = AM_PAGE_DASHBOARD && Session::getUsername();
		$hasPendingTotpVerification = AM_PAGE_DASHBOARD && !empty($_SESSION[Session::TOTP_LOGIN_SECRET_KEY]);

		self::registerResizeRoute($Router, $isAuthenticatedUser);
		self::registerApiRoutes($Router, $isAuthenticatedUser, $hasPendingTotpVerification);
		self::registerDashboardRoutes($Router, $isAuthenticatedUser, $hasPendingTotpVerification);
		self::registerFeedRoute($Router);
		self::registerMcpRoute($Router);
		self::registerPageRoutes($Router);

		self::$registered = $Router->getRoutes();
		self::$reserved = self::filterReserved(self::$registered);

		Debug::log(self::$registered, 'Registered');
		Debug::log(self::$reserved, 'Reserved');
	}

	/**
	 * Collect the non-page reserved routes
	 * that can't be used as page URLs.
	 *
	 * @param array $routes
	 * @return array
	 */
	private static function filterReserved(array $routes): array {
		$reservedUrls = array();

		foreach ($routes as $route) {
			$url = preg_replace('#^(/[\w\-\_]*).*$#i', '$1', $route['route']);

			if ($url != '/') {
				$reservedUrls[] = $url;
			}
		}

		// Get all real directories.
		foreach (FileSystem::glob(AM_BASE_DIR . '/*', GLOB_ONLYDIR) as $dir) {
			$reservedUrls[] = '/' . basename($dir);
		}

		return array_unique($reservedUrls);
	}

	/**
	 * Redirect to a given route
	 *
	 * @param string $route
	 */
	private static function redirectDashboard(string $route): void {
		header('Location: ' . AM_BASE_INDEX . AM_PAGE_DASHBOARD . $route, true, 301);
		exit();
	}

	/**
	 * Register API routes.
	 *
	 * @param Router $Router
	 * @param bool $isAuthenticatedUser
	 * @param bool $pendingTotp
	 */
	private static function registerApiRoutes(Router $Router, bool $isAuthenticatedUser, bool $pendingTotp): void {
		$apiBase = RequestHandler::API_BASE;

		$Router->register(
			"$apiBase/.*",
			function () {
				header('Content-Type: application/json; charset=utf-8');

				$Response = new Response();
				$Response->setCode(403);

				exit($Response->json());
			},
			AM_MAINTENANCE_MODE_ENABLED
		);

		$Router->register(
			"$apiBase/.*",
			function () {
				return RequestHandler::getResponse();
			},
			$isAuthenticatedUser
		);

		$Router->register(
			"$apiBase/session/(verify-totp|cancel-totp-verification)",
			function () {
				return RequestHandler::getResponse();
			},
			$pendingTotp
		);

		$Router->register(
			"$apiBase/(" . join('|', self::$publicApiRoutes) . ')',
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
				$Response->setData(array('message' => 'No session'));

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
	 * @param bool $pendingTotp
	 */
	private static function registerDashboardRoutes(Router $Router, bool $isAuthenticatedUser, bool $pendingTotp): void {
		if (!AM_PAGE_DASHBOARD) {
			return;
		}

		$hasAccounts = is_readable(UserCollection::FILE_ACCOUNTS);
		$setupCompleted = SetupWizard::isCompleted();

		$Router->register(
			AM_PAGE_DASHBOARD . '/setup',
			function () {
				return Dashboard::render();
			},
			$isAuthenticatedUser && !$setupCompleted
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '(/.*)?',
			function () {
				self::redirectDashboard('/setup');
			},
			$isAuthenticatedUser && !$setupCompleted
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '/create-user',
			function () {
				return Dashboard::render();
			},
			!$hasAccounts
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '(/.*)?',
			function () {
				self::redirectDashboard('/create-user');
			},
			!$hasAccounts
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '/create-user',
			function () {
				self::redirectDashboard('/login');
			},
			$hasAccounts
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '/(verify-totp|request-verification-code|set-password)',
			function () {
				return Dashboard::render();
			},
			$pendingTotp
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '/.*',
			function () {
				self::redirectDashboard('/verify-totp');
			},
			$pendingTotp
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '/(login|request-verification-code|set-password|verify-totp)',
			function () {
				self::redirectDashboard('/home');
			},
			$isAuthenticatedUser
		);

		$Router->register(
			AM_PAGE_DASHBOARD . '/(login|request-verification-code|set-password)',
			function () {
				return Dashboard::render();
			}
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
			}
		);
	}

	/**
	 * Register the RSS feed route.
	 *
	 * @param Router $Router
	 */
	private static function registerFeedRoute(Router $Router): void {
		$Router->register(
			AM_FEED_URL,
			array(FeedController::class, 'render'),
			AM_FEED_ENABLED
		);
	}

	/**
	 * Register the MCP resource route.
	 *
	 * @param Router $Router
	 */
	private static function registerMcpRoute(Router $Router): void {
		$Router->register(
			AM_MCP_SERVER_URL,
			array(McpController::class, 'render'),
			AM_MCP_SERVER_ENABLED
		);
	}

	/**
	 * Register all left-over routes as page routes.
	 *
	 * @param Router $Router
	 */
	private static function registerPageRoutes(Router $Router): void {
		$Router->register(
			'/',
			function () {
				header(('Location: ' . AM_BASE_URL . '/' . I18n::get()->getLanguage()));
				exit();
			},
			AM_I18N_ENABLED
		);

		$Router->register(
			'/.*',
			array(PageController::class, 'render')
		);
	}

	/**
	 * Register image routes.
	 *
	 * @param Router $Router
	 * @param bool $isAuthenticatedUser
	 */
	private static function registerResizeRoute(Router $Router, bool $isAuthenticatedUser): void {
		$Router->register(
			'/_resize',
			array(ImageController::class, 'resize'),
			$isAuthenticatedUser
		);
	}
}
