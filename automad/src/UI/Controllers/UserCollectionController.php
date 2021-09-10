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

namespace Automad\UI\Controllers;

use Automad\Core\Request;
use Automad\UI\Components\Grid\Users;
use Automad\UI\Models\UserCollectionModel;
use Automad\UI\Response;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The user collection class provides all methods for creating and loading user accounts.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class UserCollectionController {
	/**
	 * Add user account based on $_POST.
	 *
	 * @return Response the response object
	 */
	public static function createUser() {
		$Response = new Response();

		$username = Request::post('username');
		$password1 = Request::post('password1');
		$password2 = Request::post('password2');
		$email = Request::post('email');

		if (!self::validUsername($username)) {
			return self::invalidUsernameResponse();
		}

		$UserCollectionModel = new UserCollectionModel();

		if ($error = $UserCollectionModel->createUser($username, $password1, $password2, $email)) {
			$Response->setError($error);

			return $Response;
		}

		if ($error = $UserCollectionModel->save()) {
			$Response->setError($error);

			return $Response;
		}

		$Response->setSuccess(Text::get('success_added') . ' "' . $username . '"');

		return $Response;
	}

	/**
	 * Optionally remove posted accounts and
	 * return the accounts grid.
	 *
	 * @return Response the response object
	 */
	public static function edit() {
		$Response = new Response();
		$UserCollectionModel = new UserCollectionModel();

		if ($users = Request::post('delete')) {
			$Response->setError($UserCollectionModel->delete($users));

			if (!$Response->getError()) {
				$Response->setSuccess(Text::get('success_remove') . ' "' . implode('", "', $users) . '"');
			}
		}

		$Response->setHtml(Users::render($UserCollectionModel->users));

		return $Response;
	}

	/**
	 * Install the first user account.
	 *
	 * @return string Error message in case of an error.
	 */
	public static function install() {
		if (!empty($_POST)) {
			if (!self::validUsername(Request::post('username'))) {
				$Response = self::invalidUsernameResponse();

				return $Response->getError();
			}

			$UserCollectionModel = new UserCollectionModel();

			if ($error = $UserCollectionModel->createUser(
				Request::post('username'),
				Request::post('password1'),
				Request::post('password2'),
				Request::post('email')
			)) {
				return $error;
			}

			header('Expires: -1');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Type: application/octet-stream');
			header('Content-Transfer-Encoding: binary');
			header('Content-Disposition: attachment; filename=' . basename(AM_FILE_ACCOUNTS));
			ob_end_flush();

			exit($UserCollectionModel->generatePHP());
		}
	}

	/**
	 * A response containing the invalid username error message.
	 *
	 * @return Response the response object
	 */
	private static function invalidUsernameResponse() {
		$Response = new Response();
		$Response->setError(Text::get('error_invalid_username') . ' "a-z", "A-Z", ".", "-", "_", "@"');

		return $Response;
	}

	/**
	 * Verify if a given username is valid.
	 *
	 * @param string $username
	 * @return bool true in case the username is valid
	 */
	private static function validUsername(string $username) {
		preg_match('/[^@\w\.\-]/', $username, $matches);

		return empty($matches);
	}
}
