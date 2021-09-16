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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Models;

use Automad\Types\User;
use Automad\UI\Response;
use Automad\UI\Utils\Session;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The user model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class UserModel {
	/**
	 * Change a user password
	 *
	 * @param string $username
	 * @param string $currentPassword
	 * @param string $newPassword
	 */
	public function changePassword(string $username, string $currentPassword, string $newPassword) {
		$Response = new Response();
		$UserCollectionModel = new UserCollectionModel();
		$User = $UserCollectionModel->getUser($username);

		if ($User->verifyPassword($currentPassword)) {
			$User->setPasswordHash($newPassword);
			$UserCollectionModel->updateUser($User);
			$Response->setError($UserCollectionModel->save());

			if (!$Response->getError()) {
				$Response->setSuccess(Text::get('success_password_changed'));
			}
		} else {
			$Response->setError(Text::get('error_password_current'));
		}

		return $Response;
	}

	/**
	 * Handle password resetting.
	 *
	 * @param User $User
	 * @param string $newPassword1
	 * @param string $newPassword2
	 * @return string|null an error message or null
	 */
	public function resetPassword(User $User, string $newPassword1, string $newPassword2) {
		$UserCollectionModel = new UserCollectionModel();

		if ($newPassword1 === $newPassword2) {
			$User->setPasswordHash($newPassword1);
			$UserCollectionModel->updateUser($User);

			if ($error = $UserCollectionModel->save()) {
				return $error;
			} else {
				Session::clearResetTokenHash();

				return null;
			}
		} else {
			return Text::get('error_password_repeat');
		}
	}

	/**
	 * Send password reset token and store it in session.
	 *
	 * @param User $User
	 * @return string|null an error message or null
	 */
	public function sendPasswordResetToken(User $User) {
		if ($email = $User->email) {
			$token = strtoupper(substr(hash('sha256', microtime() . $User->getPasswordHash()), 0, 16));
			$tokenHash = password_hash($token, PASSWORD_DEFAULT);
			Session::setResetTokenHash($User->name, $tokenHash);

			$domain = $_SERVER['SERVER_NAME'] . AM_BASE_URL;

			$subject = "Automad: Password Reset on $domain";
			$message = "Dear $User->name,\r\n\r\na password reset has been requested for your account on $domain.\r\n" .
					   "The following token can be used to reset your password:\r\n\r\n$token\r\n\r\n" .
					   "In case you did not request the reset yourself, you can ignore this message.\r\n\r\n" .
					   'Automad';

			if (!mail($email, $subject, $message)) {
				return Text::get('error_send_email');
			}
		} else {
			return Text::get('error_user_no_email');
		}
	}

	/**
	 * Verify if the passed username/toke combination matches a token hash in the session data array.
	 *
	 * @param string $username
	 * @param string $token
	 * @return bool true if verified
	 */
	public function verifyPasswordResetToken(string $username, string $token) {
		$tokenHash = Session::getResetTokenHash($username);

		if ($tokenHash) {
			return password_verify($token, $tokenHash);
		}
	}
}
