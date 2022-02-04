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
 * Copyright (c) 2016-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers;

use Automad\API\Response;
use Automad\Auth\Session;
use Automad\Core\Request;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Session controller class provides all methods related to a user session.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SessionController {
	/**
	 * Verify login information based on $_POST.
	 *
	 * /api/Session/login
	 *
	 * @return string Error message in case of an error.
	 */
	public static function login() {
		$Response = new Response();

		if (Session::login(Request::post('name-or-email'), Request::post('password'))) {
			$Response->setRedirect(AM_BASE_INDEX . AM_PAGE_DASHBOARD);
		} else {
			$Response->setError(Text::get('error_login'));
		}

		return $Response;
	}

	/**
	 * Log out user.
	 *
	 * /api/Session/logout
	 *
	 * @return bool true on success
	 */
	public static function logout() {
		return Session::logout();
	}
}
