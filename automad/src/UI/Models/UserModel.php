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

namespace Automad\UI\Models;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The User class provides all methods related to a user account.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class UserModel {
	/**
	 * Return the currently logged in user.
	 *
	 * @return string Username
	 */
	public static function getName() {
		if (isset($_SESSION['username'])) {
			return $_SESSION['username'];
		}
	}

	/**
	 * Verify login information based on $_POST.
	 *
	 * @param string $username
	 * @param string $password
	 * @return bool false on error
	 */
	public static function login(string $username, string $password) {
		$accounts = AccountsModel::get();

		if (isset($accounts[$username]) && AccountsModel::passwordVerified($password, $accounts[$username])) {
			session_regenerate_id(true);
			$_SESSION['username'] = $username;

			// In case of using a proxy,
			// it is safer to just refresh the current page instead of rebuilding the currently requested URL.
			header('Refresh:0');

			die;
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
}
