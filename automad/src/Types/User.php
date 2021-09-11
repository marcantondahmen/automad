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

namespace Automad\Types;

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
	 * @param string|null $email
	 * @param bool $convertLegacy
	 */
	public function __construct(string $name, string $password, ?string $email = null, ?bool $convertLegacy = false) {
		$this->name = $name;
		$this->email = $email;
		$this->setPasswordHash($password);

		// Legacy account files (pre version 1.9) can't be unserialized to User objects.
		// In case of reading such a legacy file, User objects have to be constructed.
		// Since in such case there is no clear password but only an already hashed one instead,
		// The password hash property has to be overwritten with that already existing hash.
		if ($convertLegacy && strpos($password, '$2y$') === 0) {
			$this->passwordHash = $password;
		}
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
		$this->name = $properties['name'];
		$this->email = $properties['email'];
		$this->passwordHash = $properties['passwordHash'];
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
