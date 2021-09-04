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
use Automad\UI\Models\AccountsModel;
use Automad\UI\Models\UserModel;
use Automad\UI\Response;
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
					// Get all accounts from file.
					$accounts = AccountsModel::get();

					if (AccountsModel::passwordVerified($currentPassword, $accounts[UserModel::getName()])) {
						// Change entry for current user with accounts array.
						$accounts[UserModel::getName()] = AccountsModel::passwordHash($newPassword1);

						// Write array with all accounts back to file.
						if (AccountsModel::write($accounts)) {
							$Response->setSuccess(Text::get('success_password_changed'));
						} else {
							$Response->setError(Text::get('error_permission') . '<p>' . AM_FILE_ACCOUNTS . '</p>');
						}
					} else {
						$Response->setError(Text::get('error_password_current'));
					}
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
	 * Verify login information based on $_POST.
	 *
	 * @return string Error message in case of an error.
	 */
	public static function login() {
		if (!empty($_POST)) {
			if (($username = Request::post('username')) && ($password = Request::post('password'))) {
				if (!UserModel::login($username, $password)) {
					return Text::get('error_login');
				}
			} else {
				return Text::get('error_login');
			}
		}
	}

	/**
	 * Log out user.
	 *
	 * @return boolean true on success
	 */
	public static function logout() {
		return UserModel::logout();
	}
}
