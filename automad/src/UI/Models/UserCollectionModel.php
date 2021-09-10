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
class UserCollectionModel {
	/**
	 * The collection of existing user objects.
	 */
	public $users;

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->users = $this->loadUsers();
	}

	/**
	 * Add user account.
	 *
	 * @param string $username
	 * @param string $password1
	 * @param string $password2
	 * @param string|null $email
	 * @return string an error message or false on success.
	 */
	public function createUser(string $username, string $password1, string $password2, ?string $email = null) {
		if ($username && $password1 && $password2) {
			// Check if password1 equals password2.
			if ($password1 == $password2) {
				// Check, if user exists already.
				if (!isset($this->users[$username])) {
					// Add user to accounts array.
					$this->users[$username] = new User($username, $password1, $email);
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
	public function delete(array $users) {
		if (is_array($users)) {
			// Only delete users from list, if accounts.txt is writable.
			// It is important, to verify write access here, to make sure that all accounts stored in account.txt are also returned in the HTML.
			// Otherwise, they would be deleted from the array without actually being deleted from the file, in case accounts.txt is write protected.
			// So it is not enough to just check, if file_put_contents was successful, because that would be simply too late.
			if (is_writable(AM_FILE_ACCOUNTS)) {
				foreach ($users as $userToDelete) {
					if (isset($this->users[$userToDelete])) {
						unset($this->users[$userToDelete]);
					}
				}

				$this->save();
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
	 * @return string the PHP code
	 */
	public function generatePHP() {
		return "<?php\ndefined('AUTOMAD') or die();\nreturn unserialize('" . serialize($this->users) . "');";
	}

	/**
	 * Return a user.
	 *
	 * @param string $name
	 * @return User the requested user account
	 */
	public function getUser(string $name) {
		if (array_key_exists($name, $this->users)) {
			return $this->users[$name];
		}

		return null;
	}

	/**
	 * Save the accounts array as PHP to AM_FILE_ACCOUNTS.
	 *
	 * @return bool|string False on success or an error message
	 */
	public function save() {
		ksort($this->users);

		if (!FileSystem::write(AM_FILE_ACCOUNTS, $this->generatePHP())) {
			return Text::get('error_permission') . '<p>' . AM_FILE_ACCOUNTS . '</p>';
		}

		if (function_exists('opcache_invalidate')) {
			opcache_invalidate(AM_FILE_ACCOUNTS, true);
		}

		return false;
	}

	/**
	 * Update or add a single user object.
	 *
	 * @param User $User
	 */
	public function updateUser(User $User) {
		$this->users[$User->name] = $User;
	}

	/**
	 * Get the accounts array by including the accounts PHP file.
	 *
	 * @see User
	 * @return array The registered accounts
	 */
	private function loadUsers() {
		if (!is_readable(AM_FILE_ACCOUNTS)) {
			return array();
		}

		$accounts = (include AM_FILE_ACCOUNTS);

		// Check for legacy accounts format and convert it to the new one.
		foreach ($accounts as $name => $data) {
			if (is_string($data)) {
				$accounts[$name] = new User($name, $data);
			}
		}

		return $accounts;
	}
}
