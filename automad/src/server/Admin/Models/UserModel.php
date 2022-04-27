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

namespace Automad\Admin\Models;

use Automad\Admin\API\Response;
use Automad\Admin\Session;
use Automad\Admin\UI\Templates\PasswordResetEmail;
use Automad\Admin\UI\Utils\Messenger;
use Automad\Admin\UI\Utils\Text;
use Automad\Admin\User;

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
	 * @return Response the response object
	 */
	public function changePassword(string $username, string $currentPassword, string $newPassword) {
		$Response = new Response();
		$Messenger = new Messenger();
		$UserCollectionModel = new UserCollectionModel();
		$User = $UserCollectionModel->getUser($username);

		if ($User->verifyPassword($currentPassword)) {
			$User->setPasswordHash($newPassword);

			if ($UserCollectionModel->save($Messenger)) {
				$Response->setSuccess(Text::get('passwordChangedSuccess'));
			} else {
				$Response->setError($Messenger->getError());
			}
		} else {
			$Response->setError(Text::get('currentPasswordError'));
		}

		return $Response;
	}

	/**
	 * Handle password resetting.
	 *
	 * @param string $username
	 * @param string $newPassword1
	 * @param string $newPassword2
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public function resetPassword(string $username, string $newPassword1, string $newPassword2, Messenger $Messenger) {
		if ($newPassword1 !== $newPassword2) {
			$Messenger->setError(Text::get('passwordRepeatError'));

			return false;
		}

		$UserCollectionModel = new UserCollectionModel();
		$User = $UserCollectionModel->getUser($username);
		$User->setPasswordHash($newPassword1);

		if (!$UserCollectionModel->save($Messenger)) {
			return false;
		}

		Session::clearResetTokenHash();

		return true;
	}

	/**
	 * Send password reset token and store it in session.
	 *
	 * @param User $User
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public function sendPasswordResetToken(User $User, Messenger $Messenger) {
		$email = $User->email;

		if (!$email) {
			$Messenger->setError(Text::get('error_user_no_email'));

			return false;
		}

		$token = strtoupper(substr(hash('sha256', microtime() . $User->getPasswordHash()), 0, 16));
		$tokenHash = password_hash($token, PASSWORD_DEFAULT);
		Session::setResetTokenHash($User->name, $tokenHash);

		$website = $_SERVER['SERVER_NAME'] . AM_BASE_URL;
		$subject = 'Automad: ' . Text::get('emailResetPasswordSubject');
		$message = PasswordResetEmail::render($website, $User->name, $token);
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8';

		if (!mail($email, $subject, $message, $headers)) {
			$Messenger->setError(Text::get('sendMailError'));

			return false;
		}

		return true;
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