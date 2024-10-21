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
 * Copyright (c) 2016-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Automad\Models\UserCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Session util class provides all methods related to a user session.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Session {
	const CSRF_TOKEN_KEY = 'csrf';
	const I18N_LANG = 'lang';
	const RESET_TOKEN_KEY = 'reset';
	const USERNAME_KEY = 'username';

	/**
	 * Clears the reset token hash
	 */
	public static function clearResetTokenHash(): void {
		unset($_SESSION[self::RESET_TOKEN_KEY]);
	}

	/**
	 * Get the CSRF token for the current session.
	 *
	 * @return string the CSRF token stored in the session
	 */
	public static function getCsrfToken(): string {
		return $_SESSION[self::CSRF_TOKEN_KEY] ?? self::createCsrfToken();
	}

	/**
	 * Return the reset token hash for a given user.
	 *
	 * @param string $username
	 * @return string the token hash
	 */
	public static function getResetTokenHash(string $username): string {
		if (isset($_SESSION[self::RESET_TOKEN_KEY])) {
			return $_SESSION[self::RESET_TOKEN_KEY][$username] ?? '';
		}

		return '';
	}

	/**
	 * Return the currently logged in user.
	 *
	 * @return string Username
	 */
	public static function getUsername(): string {
		return $_SESSION[self::USERNAME_KEY] ?? '';
	}

	/**
	 * Verify login information based on $_POST.
	 *
	 * @param string $nameOrEmail
	 * @param string $password
	 * @return bool false on error
	 */
	public static function login(string $nameOrEmail, string $password): bool {
		$UserCollection = new UserCollection();
		$User = $UserCollection->getUser($nameOrEmail);

		if (empty($User)) {
			return false;
		}

		if ($User->verifyPassword($password)) {
			session_regenerate_id(true);
			$_SESSION[self::USERNAME_KEY] = $User->name;
			self::createCsrfToken();

			return true;
		}

		return false;
	}

	/**
	 * Log out user.
	 *
	 * @return bool true on success
	 */
	public static function logout(): bool {
		unset($_SESSION);
		$success = session_destroy();

		if (!isset($_SESSION) && $success) {
			return true;
		}

		return false;
	}

	/**
	 * Set the reset token hash for a given user.
	 *
	 * @param string $username
	 * @param string $tokenHash
	 */
	public static function setResetTokenHash(string $username, string $tokenHash): void {
		$_SESSION[self::RESET_TOKEN_KEY] = array($username => $tokenHash);
	}

	/**
	 * Verify a given CSRF token.
	 *
	 * @param string $token
	 * @return bool true if the token is valid
	 */
	public static function verifyCsrfToken(string $token): bool {
		if (empty($_SESSION[self::CSRF_TOKEN_KEY])) {
			return false;
		}

		return $token === $_SESSION[self::CSRF_TOKEN_KEY];
	}

	/**
	 * Create a CSRF protection token.
	 *
	 * @return string the created token
	 */
	private static function createCsrfToken(): string {
		$_SESSION[self::CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));

		return $_SESSION[self::CSRF_TOKEN_KEY];
	}
}
