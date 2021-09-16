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
use Automad\UI\Components\Alert\Danger;
use Automad\UI\Components\Layout\PasswordReset\ResetForm;
use Automad\UI\Components\Layout\PasswordReset\ResetSuccess;
use Automad\UI\Components\Layout\PasswordReset\TokenRequestForm;
use Automad\UI\Models\UserCollectionModel;
use Automad\UI\Models\UserModel;
use Automad\UI\Response;
use Automad\UI\Utils\Session;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The User class provides all methods related to a user account.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class UserController {
	/**
	 * Change the password of the currently logged in user based on $_POST.
	 *
	 * @return Response the response object
	 */
	public static function changePassword() {
		$Response = new Response();
		$currentPassword = Request::post('current-password');
		$newPassword1 = Request::post('new-password1');
		$newPassword2 = Request::post('new-password2');

		if ($currentPassword && $newPassword1 && $newPassword2) {
			if ($newPassword1 == $newPassword2) {
				if ($currentPassword != $newPassword1) {
					$UserModel = new UserModel();
					$Response = $UserModel->changePassword(
						Session::getUsername(),
						$currentPassword,
						$newPassword1
					);
				} else {
					$Response->setError(Text::get('error_password_reuse'));
				}
			} else {
				$Response->setError(Text::get('error_password_repeat'));
			}
		} else {
			$Response->setError(Text::get('error_form'));
		}

		return $Response;
	}

	/**
	 * Edit user account info such as username and email.
	 *
	 * @return Response the response
	 */
	public static function edit() {
		$Response = new Response();
		$username = Request::post('username');
		$email = Request::post('email');

		$UserCollectionModel = new UserCollectionModel();
		$Response->setError($UserCollectionModel->editCurrentUserInfo($username, $email));

		if (empty($Response->getError())) {
			$Response->setError($UserCollectionModel->save());

			if (empty($Response->getError())) {
				$Response->setReload(true);
			}
		}

		return $Response;
	}

	/**
	 * Reset a user password by email.
	 *
	 * @return string the form HTML
	 */
	public static function resetPassword() {
		$UserModel = new UserModel();
		$UserCollectionModel = new UserCollectionModel();

		$username = trim(Request::post('username'));
		$token = Request::post('token');
		$newPassword1 = Request::post('password1');
		$newPassword2 = Request::post('password2');

		$User = $UserCollectionModel->getUser($username);

		if ($username && !$User) {
			return TokenRequestForm::render(Text::get('error_user_not_found'));
		}

		if ($User && $token && $newPassword1 && $newPassword2) {
			if ($UserModel->verifyPasswordResetToken($User->name, $token)) {
				if ($error = $UserModel->resetPassword($User, $newPassword1, $newPassword2)) {
					return ResetForm::render($User->name, $error);
				} else {
					return ResetSuccess::render();
				}
			} else {
				return ResetForm::render($User->name, Text::get('error_password_reset_verification'));
			}
		}

		if ($User) {
			if ($error = $UserModel->sendPasswordResetToken($User)) {
				return TokenRequestForm::render($error);
			}

			return ResetForm::render($User->name);
		}

		return TokenRequestForm::render();
	}
}
