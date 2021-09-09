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
					// Get all accounts from file.
					$accounts = AccountsModel::get();

					if (AccountsModel::passwordVerified($currentPassword, $accounts[Session::getUsername()])) {
						// Change entry for current user with accounts array.
						$accounts[Session::getUsername()] = AccountsModel::passwordHash($newPassword1);

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
}
