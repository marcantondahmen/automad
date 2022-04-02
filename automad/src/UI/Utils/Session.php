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

namespace Automad\UI\Utils;

use Automad\UI\Models\UserCollectionModel;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Session model class provides all methods related to a user session.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Session {
	/**
	 * Clears the reset token hash
	 */
	public static function clearResetTokenHash() {
		unset($_SESSION['reset']);
	}

	/**
	 * Return the reset token hash for a given user.
	 *
	 * @param string $username
	 * @return string the token hash
	 */
	public static function getResetTokenHash(string $username) {
		if (isset($_SESSION['reset'])) {
			if (isset($_SESSION['reset'][$username])) {
				return $_SESSION['reset'][$username];
			}
		}

		return '';
	}

	/**
	 * Return the currently logged in user.
	 *
	 * @return string Username
	 */
	public static function getUsername() {
		if (isset($_SESSION['username'])) {
			return $_SESSION['username'];
		}

		return '';
	}

	/**
	 * Verify login information based on $_POST.
	 *
	 * @param string $nameOrEmail
	 * @param string $password
	 * @return bool false on error
	 */
	public static function login(string $nameOrEmail, string $password) {
		$UserCollectionModel = new UserCollectionModel();
		$User = $UserCollectionModel->getUser($nameOrEmail);

		if (empty($User)) {
			return false;
		}

		if ($User->verifyPassword($password)) {
			session_regenerate_id(true);
			$_SESSION['username'] = $User->name;

			// In case of using a proxy,
			// it is safer to just refresh the current page instead of rebuilding the currently requested URL.
			header('Refresh:0');

			exit();
		}

		return false;
	}

	/**
	 * Log out user.
	 *
	 * @return bool true on success
	 */
	public static function logout() {
		unset($_SESSION);
		$success = session_destroy();

		if (!isset($_SESSION) && $success) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Set the reset token hash for a given user.
	 *
	 * @param string $username
	 * @param string $tokenHash
	 */
	public static function setResetTokenHash(string $username, string $tokenHash) {
		$_SESSION['reset'] = array($username => $tokenHash);
	}
}
