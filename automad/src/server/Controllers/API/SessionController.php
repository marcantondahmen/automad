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
 * Copyright (c) 2016-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Request;
use Automad\Core\Session;
use Automad\Core\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Session controller class provides all methods related to a user session.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SessionController {
	/**
	 * Verify login information based on $_POST.
	 *
	 * @return Response the Response object
	 */
	public static function login(): Response {
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
	public static function logout(): Response {
		$Response = new Response();

		if (Session::logout()) {
			$Response->setSuccess(Text::get('signedOutSuccess'))->setRedirect('login');
		}

		return $Response;
	}

	/**
	 * A simple testing endpoint to verify if a browser tab has a valid CSRF token.
	 *
	 * @return Response the Response object
	 */
	public static function validate() {
		$Response = new Response();
		$Response->setSuccess('OK');

		return $Response;
	}
}
