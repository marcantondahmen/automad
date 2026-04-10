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
 * Copyright (c) 2016-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Auth\Session;
use Automad\Core\Request;
use Automad\Core\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Session controller class provides all methods related to a user session.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class SessionController {
	/**
	 * Cancel a pending TOTP verification.
	 *
	 * @return Response
	 */
	public static function cancelTotpVerification(): Response {
		$Response = new Response();

		Session::resetTotpVerification();

		return $Response->setReload(true);
	}

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
			$Response->setSuccess(Text::get('signedOutSuccess'))->setReload(true);
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

	/**
	 * Verify a TOTP code and sign user in.
	 *
	 * @return Response
	 */
	public static function verifyTotp(): Response {
		$Response = new Response();

		if (Session::verifyTotp(Request::post('code'))) {
			return $Response->setReload(true);
		}

		return $Response->setError(Text::get('verifyTotpError'));
	}
}
