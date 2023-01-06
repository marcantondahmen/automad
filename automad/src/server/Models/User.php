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

namespace Automad\Models;

use Automad\Admin\Session;
use Automad\Admin\UI\Templates\PasswordResetEmail;
use Automad\Admin\UI\Utils\Messenger;
use Automad\Admin\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The User type is a custom data type that stores all data that is related to a user.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class User {
	/**
	 * The user's email.
	 */
	public $email;

	/**
	 * The username.
	 */
	public $name;

	/**
	 * The encrypted password.
	 */
	private $passwordHash;

	/**
	 * The constructor.
	 *
	 * @param string $name
	 * @param string $password
	 * @param string $email
	 */
	public function __construct(string $name, string $password, string $email = '') {
		$this->name = $name;
		$this->email = $email;
		$this->setPasswordHash($password);
	}

	/**
	 * Serialize object.
	 *
	 * @return array the associative array of properties
	 */
	public function __serialize() {
		return array(
			'name' => $this->name,
			'email' => $this->email,
			'passwordHash' => $this->passwordHash
		);
	}

	/**
	 * Unserialize object.
	 *
	 * @param array $properties
	 */
	public function __unserialize(array $properties) {
		$properties = array_merge(array('email' => ''), $properties);

		$this->name = $properties['name'];
		$this->email = $properties['email'];
		$this->passwordHash = $properties['passwordHash'];
	}

	/**
	 * Change a user password
	 *
	 * @param string $currentPassword
	 * @param string $newPassword
	 * @param UserCollection $UserCollection
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public function changePassword(string $currentPassword, string $newPassword, UserCollection $UserCollection, Messenger $Messenger) {
		if ($this->verifyPassword($currentPassword)) {
			$this->setPasswordHash($newPassword);

			if ($UserCollection->save($Messenger)) {
				$Messenger->setSuccess(Text::get('passwordChangedSuccess'));

				return true;
			}

			return false;
		}

		$Messenger->setError(Text::get('currentPasswordError'));

		return false;
	}

	/**
	 * Get a hashed version of a user password.
	 *
	 * @return string the hashed password
	 */
	public function getPasswordHash() {
		return $this->passwordHash;
	}

	/**
	 * Handle password resetting.
	 *
	 * @param string $newPassword1
	 * @param string $newPassword2
	 * @param UserCollection $UserCollection
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public function resetPassword(string $newPassword1, string $newPassword2, UserCollection $UserCollection, Messenger $Messenger) {
		if ($newPassword1 !== $newPassword2) {
			$Messenger->setError(Text::get('passwordRepeatError'));

			return false;
		}

		$this->setPasswordHash($newPassword1);

		if (!$UserCollection->save($Messenger)) {
			return false;
		}

		Session::clearResetTokenHash();

		return true;
	}

	/**
	 * Send password reset token and store it in session.
	 *
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public function sendPasswordResetToken(Messenger $Messenger) {
		$email = $this->email;

		if (!$email) {
			$Messenger->setError(Text::get('error_user_no_email'));

			return false;
		}

		$token = strtoupper(substr(hash('sha256', microtime() . $this->getPasswordHash()), 0, 16));
		$tokenHash = password_hash($token, PASSWORD_DEFAULT);
		Session::setResetTokenHash($this->name, $tokenHash);

		$website = $_SERVER['SERVER_NAME'] . AM_BASE_URL;
		$subject = 'Automad: ' . Text::get('emailResetPasswordSubject');
		$message = PasswordResetEmail::render($website, $this->name, $token);
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8';

		if (!mail($email, $subject, $message, $headers)) {
			$Messenger->setError(Text::get('sendMailError'));

			return false;
		}

		return true;
	}

	/**
	 * Store a hashed version of a given clear text password.
	 *
	 * @param string $password
	 */
	public function setPasswordHash(string $password) {
		$this->passwordHash = $this->hash($password);
	}

	/**
	 * Verify if a password matches its saved hashed version.
	 *
	 * @param string $password
	 * @return bool true if the password is verified
	 */
	public function verifyPassword(string $password) {
		$hash = $this->passwordHash;

		return ($hash === crypt($password, $hash));
	}

	/**
	 * Verify if the passed username/toke combination matches a token hash in the session data array.
	 *
	 * @param string $token
	 * @return bool true if verified
	 */
	public function verifyPasswordResetToken(string $token) {
		$tokenHash = Session::getResetTokenHash($this->name);

		if ($tokenHash) {
			return password_verify($token, $tokenHash);
		}
	}

	/**
	 * Create hash from password to store in accounts.txt.
	 *
	 * @param string $password
	 * @return string Hashed/salted password
	 */
	private function hash(string $password) {
		$salt = '$2y$10$' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 22);

		return crypt($password, $salt);
	}
}
