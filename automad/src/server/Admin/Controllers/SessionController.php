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
 * Copyright (c) 2016-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Admin\Controllers;

use Automad\Admin\API\Response;
use Automad\Admin\Session;
use Automad\Admin\UI\Utils\Text;
use Automad\Core\Request;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Session controller class provides all methods related to a user session.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2022 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SessionController {
	/**
	 * Verify login information based on $_POST.
	 *
	 * @return Response the Response object
	 */
	public static function login() {
		$Response = new Response();

		if (Session::login(Request::post('name-or-email'), Request::post('password'))) {
			return $Response->setReload(true);
		}

		return $Response->setError(Text::get('signInError'));
	}

	/**
	 * Log out user.
	 *
	 * @return Response the Response object
	 */
	public static function logout() {
		$Response = new Response();

		if (Session::logout()) {
			$Response->setSuccess(Text::get('signedOutSuccess'));

			return $Response->setRedirect('login');
		}
	}

	/**
	 * A simple testing endpoint to verify if a browser tab has a valid app id and CSRF token.
	 *
	 * @return Response the Response object
	 */
	public static function validate() {
		$Response = new Response();
		$Response->setSuccess('OK');

		return $Response;
	}
}
