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
use Automad\UI\Utils\Messenger;
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
		$Messenger = new Messenger();
		$UserCollectionModel = new UserCollectionModel();

		$username = Request::post('username');
		$email = Request::post('email');

		if ($UserCollectionModel->editCurrentUserInfo($username, $email, $Messenger)) {
			if ($UserCollectionModel->save($Messenger)) {
				$Response->setReload(true);
			}
		}

		$Response->setError($Messenger->getError());

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
		$Messenger = new Messenger();

		// Only one field will be defined, so they can just be concatenated here.
		$nameOrEmail = trim(Request::post('name-or-email') . Request::post('username'));

		$token = Request::post('token');
		$newPassword1 = Request::post('password1');
		$newPassword2 = Request::post('password2');

		$User = $UserCollectionModel->getUser($nameOrEmail);

		if ($nameOrEmail && !$User) {
			return TokenRequestForm::render(Text::get('error_user_not_found'));
		}

		if ($User && $token && $newPassword1 && $newPassword2) {
			if ($UserModel->verifyPasswordResetToken($User->name, $token)) {
				if ($UserModel->resetPassword($User->name, $newPassword1, $newPassword2, $Messenger)) {
					return ResetSuccess::render();
				} else {
					return ResetForm::render($User->name, $Messenger->getError());
				}
			} else {
				return ResetForm::render($User->name, Text::get('error_password_reset_verification'));
			}
		}

		if ($User) {
			if ($UserModel->sendPasswordResetToken($User, $Messenger)) {
				return ResetForm::render($User->name);
			}

			return TokenRequestForm::render($Messenger->getError());
		}

		return TokenRequestForm::render();
	}
}
