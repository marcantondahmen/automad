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
use Automad\UI\Components\Email\InvitationEmail;
use Automad\UI\Utils\FileSystem;
use Automad\UI\Utils\Messenger;
use Automad\UI\Utils\Session;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The user collection model.
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
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public function createUser(string $username, string $password1, string $password2, ?string $email = null, Messenger $Messenger) {
		$username = trim($username);
		$email = trim($email);

		if (!$this->validUsername($username)) {
			$Messenger->setError($this->invalidUsernameError());

			return false;
		}

		if ($email && !$this->validEmail($email)) {
			$Messenger->setError($this->invalidEmailError());

			return false;
		}

		if (!$username || !$password1 || !$password2) {
			$Messenger->setError(Text::get('error_form'));

			return false;
		}

		if ($password1 != $password2) {
			$Messenger->setError(Text::get('error_password_repeat'));

			return false;
		}

		if (isset($this->users[$username])) {
			$Messenger->setError('"' . $username . '" ' . Text::get('error_existing'));

			return false;
		}

		$this->users[$username] = new User($username, $password1, $email);

		return true;
	}

	/**
	 * Delete one ore more user accounts.
	 *
	 * @param array $users
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public function delete(array $users, Messenger $Messenger) {
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

				return $this->save($Messenger);
			} else {
				$Messenger->setError(Text::get('error_permission') . '<p>' . AM_FILE_ACCOUNTS . '</p>');

				return false;
			}
		}

		return false;
	}

	/**
	 * Edit info of the currently logged in user.
	 *
	 * @param string $username
	 * @param string $email
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public function editCurrentUserInfo(string $username, string $email, Messenger $Messenger) {
		$User = $this->getUser(Session::getUsername());

		if (!$User || !$username) {
			$Messenger->setError(Text::get('error_form'));

			return false;
		}

		$username = trim($username);
		$email = trim($email);

		if (!$this->validUsername($username)) {
			$Messenger->setError($this->invalidUsernameError());

			return false;
		}

		if (!$this->validEmail($email)) {
			$Messenger->setError($this->invalidEmailError());

			return false;
		}

		if ($User->name != $username) {
			if (!array_key_exists($username, $this->users)) {
				unset($this->users[$User->name]);
				$User->name = $username;
				$_SESSION['username'] = $username;
			} else {
				$Messenger->setError('"' . $username . '" ' . Text::get('error_existing'));

				return false;
			}
		}

		$User->email = $email;
		$this->updateUser($User);

		return true;
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
		return "<?php\ndefined('AUTOMAD') or die();\nreturn '" . serialize($this->users) . "';";
	}

	/**
	 * Return a user.
	 *
	 * @param string $name
	 * @return User|null the requested user account
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
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public function save(Messenger $Messenger) {
		ksort($this->users);

		if (!FileSystem::write(AM_FILE_ACCOUNTS, $this->generatePHP())) {
			$Messenger->setError(Text::get('error_permission') . '<p>' . AM_FILE_ACCOUNTS . '</p>');

			return false;
		}

		if (function_exists('opcache_invalidate')) {
			opcache_invalidate(AM_FILE_ACCOUNTS, true);
		}

		return true;
	}

	/**
	 * Send invitation email.
	 *
	 * @param string $username
	 * @param string $email
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public function sendInvitation(string $username, string $email, Messenger $Messenger) {
		$protocol = 'http';
		$port = '';

		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
			$protocol = 'https';
		}

		if (!in_array($_SERVER['SERVER_PORT'], array(80, 443))) {
			$port = ":$_SERVER[SERVER_PORT]";
		}

		$website = $_SERVER['SERVER_NAME'] . AM_BASE_URL;
		$link = $protocol . '://' . $_SERVER['SERVER_NAME'] . $port .
				AM_BASE_INDEX . AM_PAGE_DASHBOARD .
				'?view=ResetPassword&username=' . urlencode($username);
		$subject = 'Automad: You have been added as a new user';
		$message = InvitationEmail::render($website, $username, $link);
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8';

		if (!mail($email, $subject, $message, $headers)) {
			$Messenger->setError(Text::get('error_send_email'));

			return false;
		}

		return true;
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
	 * The invalid email error message.
	 *
	 * @return string the error message
	 */
	private function invalidEmailError() {
		return Text::get('error_invalid_email');
	}

	/**
	 * The invalid username error message.
	 *
	 * @return string the error message
	 */
	private function invalidUsernameError() {
		return Text::get('error_invalid_username') . ' "a-z", "A-Z", ".", "-", "_", "@"';
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
		if (is_array($accounts)) {
			foreach ($accounts as $name => $data) {
				if (is_string($data)) {
					$accounts[$name] = new User($name, $data, null, true);
				}
			}

			return $accounts;
		}

		return unserialize($accounts);
	}

	/**
	 * Verify if a given email address is valid.
	 *
	 * @param string $email
	 * @return bool true in case the username is valid
	 */
	private function validEmail(?string $email = null) {
		preg_match('/^[a-zA-Z0-9]+[\w\.\-\_]*@[\w\.\-\_]+\.[a-zA-Z]+$/', $email, $matches);

		return $matches;
	}

	/**
	 * Verify if a given username is valid.
	 *
	 * @param string $username
	 * @return bool true in case the username is valid
	 */
	private function validUsername(string $username) {
		preg_match('/[^@\w\.\-]/', $username, $matches);

		return empty($matches);
	}
}
