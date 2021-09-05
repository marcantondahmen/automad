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

use Automad\UI\Utils\FileSystem;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Accounts model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class AccountsModel {
	/**
	 * Add user account.
	 *
	 * @param string $username
	 * @param string $password1
	 * @param string $password2
	 * @return string an error message or false on success.
	 */
	public static function add(string $username, string $password1, string $password2) {
		if ($username && $password1 && $password2) {
			// Check if password1 equals password2.
			if ($password1 == $password2) {
				// Get all accounts from file.
				$accounts = self::get();

				// Check, if user exists already.
				if (!isset($accounts[$username])) {
					// Add user to accounts array.
					$accounts[$username] = self::passwordHash($password1);
					ksort($accounts);

					// Write array with all accounts back to file.
					if (!self::write($accounts)) {
						return Text::get('error_permission') . '<p>' . AM_FILE_ACCOUNTS . '</p>';
					}
				} else {
					return '"' . $username . '" ' . Text::get('error_existing');
				}
			} else {
				return Text::get('error_password_repeat');
			}
		} else {
			return Text::get('error_form');
		}

		return false;
	}

	/**
	 * Delete one ore more user accounts.
	 *
	 * @param array $users
	 * @return string an error message or false on success.
	 */
	public static function delete(array $users) {
		if (is_array($users)) {
			// Only delete users from list, if accounts.txt is writable.
			// It is important, to verify write access here, to make sure that all accounts stored in account.txt are also returned in the HTML.
			// Otherwise, they would be deleted from the array without actually being deleted from the file, in case accounts.txt is write protected.
			// So it is not enough to just check, if file_put_contents was successful, because that would be simply too late.
			if (is_writable(AM_FILE_ACCOUNTS)) {
				$accounts = self::get();

				foreach ($users as $userToDelete) {
					if (isset($accounts[$userToDelete])) {
						unset($accounts[$userToDelete]);
					}
				}

				self::write($accounts);
			} else {
				return Text::get('error_permission') . '<p>' . AM_FILE_ACCOUNTS . '</p>';
			}
		}

		return false;
	}

	/**
	 * Generate the PHP code for the accounts file. Basically the code returns the unserialized serialized array with all users.
	 * That way, the accounts array can be stored as PHP.
	 * The accounts file has to be a PHP file for security reasons. When trying to access the file directly via the browser,
	 * it gets executed instead of revealing any user names.
	 *
	 * @param array $accounts
	 * @return string The PHP code
	 */
	public static function generatePHP(array $accounts) {
		return 	"<?php defined('AUTOMAD') or die('Direct access not permitted!');\n" .
				'return unserialize(\'' . serialize($accounts) . '\');' . "\n?>";
	}

	/**
	 * Get the accounts array by including the accounts PHP file.
	 *
	 * @return array The registered accounts
	 */
	public static function get() {
		return (include AM_FILE_ACCOUNTS);
	}

	/**
	 * Install the first user account.
	 *
	 * @param string $username
	 * @param string $password1
	 * @param string $password2
	 * @return string Error message in case of an error.
	 */
	public static function install(string $username, string $password1, string $password2) {
		if ($username && $password1 && ($password1 === $password2)) {
			$accounts = array();
			$accounts[$username] = self::passwordHash($password1);

			// Download accounts.php
			header('Expires: -1');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Type: application/octet-stream');
			header('Content-Transfer-Encoding: binary');
			header('Content-Disposition: attachment; filename=' . basename(AM_FILE_ACCOUNTS));
			ob_end_flush();
			echo self::generatePHP($accounts);
			exit();
		} else {
			return Text::get('error_form');
		}
	}

	/**
	 * Create hash from password to store in accounts.txt.
	 *
	 * @param string $password
	 * @return string Hashed/salted password
	 */
	public static function passwordHash(string $password) {
		$salt = '$2y$10$' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 22);

		return crypt($password, $salt);
	}

	/**
	 * Verify if a password matches its hashed version.
	 *
	 * @param string $password (clear text)
	 * @param string $hash (hashed password)
	 * @return bool true if the password is verified
	 */
	public static function passwordVerified(string $password, string $hash) {
		return ($hash === crypt($password, $hash));
	}

	/**
	 * Save the accounts array as PHP to AM_FILE_ACCOUNTS.
	 *
	 * @param array $accounts
	 * @return bool Success (true/false)
	 */
	public static function write(array $accounts) {
		$success = FileSystem::write(AM_FILE_ACCOUNTS, self::generatePHP($accounts));

		if ($success && function_exists('opcache_invalidate')) {
			opcache_invalidate(AM_FILE_ACCOUNTS, true);
		}

		return $success;
	}
}
