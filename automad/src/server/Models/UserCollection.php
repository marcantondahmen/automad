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
 * Copyright (c) 2021-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Models;

use Automad\Admin\Templates\InvitationEmail;
use Automad\Core\Cache;
use Automad\Core\FileSystem;
use Automad\Core\Messenger;
use Automad\Core\Session;
use Automad\Core\Text;
use Automad\System\Mail;
use Automad\System\Server;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The user collection model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class UserCollection {
	const FILE_ACCOUNTS = AM_BASE_DIR . '/config/accounts.php';

	/**
	 * The collection of existing user objects.
	 */
	private array $users;

	/**
	 * The class name of the user type.
	 */
	private string $userType = 'Automad\Models\User';

	/**
	 * The replacement for the user type class in a serialized string.
	 */
	private string $userTypeSerialized = 'O:*:"~"';

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->users = $this->load();
	}

	/**
	 * Add user account.
	 *
	 * @param string $username
	 * @param string $password1
	 * @param string $password2
	 * @param string $email
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public function createUser(
		string $username,
		string $password1,
		string $password2,
		string $email,
		Messenger $Messenger
	): bool {
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
			$Messenger->setError(Text::get('invalidFormError'));

			return false;
		}

		if ($password1 != $password2) {
			$Messenger->setError(Text::get('passwordRepeatError'));

			return false;
		}

		if ($this->getUser($username)) {
			$Messenger->setError('"' . $username . '" ' . Text::get('alreadyExists'));

			return false;
		}

		if ($this->getUser($email)) {
			$Messenger->setError('"' . $email . '" ' . Text::get('alreadyExists'));

			return false;
		}

		$this->users[] = new User($username, $password1, $email);

		return true;
	}

	/**
	 * Delete one ore more user accounts.
	 *
	 * @param array $users
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public function delete(array $users, Messenger $Messenger): bool {
		// Only delete users from list, if accounts.php is writable.
		// It is important, to verify write access here, to make sure that all accounts stored in account.txt are also returned in the HTML.
		// Otherwise, they would be deleted from the array without actually being deleted from the file, in case accounts.txt is write protected.
		// So it is not enough to just check, if file_put_contents was successful, because that would be simply too late.
		if (is_writable(UserCollection::FILE_ACCOUNTS)) {
			foreach ($users as $username) {
				$id = $this->getUserId($username);

				if (!is_null($id) && isset($this->users[$id])) {
					unset($this->users[$id]);
				}
			}

			return $this->save($Messenger);
		}

		$Messenger->setError(Text::get('permissionsDeniedError'));

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
	public function editCurrentUserInfo(
		string $username,
		string $email,
		Messenger $Messenger
	): bool {
		$id = $this->getUserId(Session::getUsername());

		if (is_null($id) || empty($this->users[$id])) {
			return false;
		}

		$User = $this->users[$id];

		// Unset temporary the array item here in order to check easily for duplicate eamils or names.
		unset($this->users[$id]);

		if (!$username) {
			$Messenger->setError(Text::get('invalidFormError'));

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

		if ($this->getUser($username)) {
			$Messenger->setError('"' . $username . '" ' . Text::get('alreadyExists'));

			return false;
		}

		if ($this->getUser($email)) {
			$Messenger->setError('"' . $email . '" ' . Text::get('alreadyExists'));

			return false;
		}

		$User->name = $username;
		$_SESSION['username'] = $username;

		$User->email = $email;

		$this->users[$id] = $User;

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
	public function generatePHP(): string {
		ksort($this->users);

		// The actual class name is replaced with a placeholder in order
		// to be able to refactor the type class in the future easily.
		$serialized = str_replace(
			'O:' . strlen($this->userType) . ':"' . $this->userType . '"',
			$this->userTypeSerialized,
			serialize($this->users)
		);

		return "<?php\ndefined('AUTOMAD') or die();\nreturn '" . $serialized . "';";
	}

	/**
	 * Return the user collection array.
	 *
	 * @return array the user collection array
	 */
	public function getCollection(): array {
		return $this->users;
	}

	/**
	 * Return a user by name or email address.
	 *
	 * @param string $nameOrEmail
	 * @return User|null the requested user account
	 */
	public function getUser(string $nameOrEmail): ?User {
		if (empty($nameOrEmail)) {
			return null;
		}

		foreach ($this->users as $User) {
			if ($nameOrEmail === $User->name || $nameOrEmail === $User->email) {
				return $User;
			}
		}

		return null;
	}

	/**
	 * Save the accounts array as PHP to FILE_ACCOUNTS.
	 *
	 * @param Messenger $Messenger
	 * @return bool true on success
	 */
	public function save(Messenger $Messenger): bool {
		if (!FileSystem::write(UserCollection::FILE_ACCOUNTS, $this->generatePHP())) {
			$Messenger->setError(Text::get('permissionsDeniedError'));

			return false;
		}

		if (function_exists('opcache_invalidate')) {
			opcache_invalidate(UserCollection::FILE_ACCOUNTS, true);
		}

		Cache::clear();

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
	public function sendInvitation(string $username, string $email, Messenger $Messenger): bool {
		$website = $_SERVER['SERVER_NAME'] ?? '' . AM_BASE_URL;
		$link = AM_SERVER . AM_BASE_INDEX . AM_PAGE_DASHBOARD . '/resetpassword?username=' . urlencode($username);
		$subject = 'Automad: ' . Text::get('emailInviteSubject');
		$message = InvitationEmail::render($website, $username, $link);

		if (!Mail::send($email, $subject, $message)) {
			$Messenger->setError(Text::get('sendMailError'));

			return false;
		}

		return true;
	}

	/**
	 * Return a user id by name or email address.
	 *
	 * @param string $nameOrEmail
	 * @return int|null the requested user id
	 */
	private function getUserId(string $nameOrEmail): ?int {
		foreach ($this->users as $id => $User) {
			if ($nameOrEmail === $User->name || $nameOrEmail === $User->email) {
				return $id;
			}
		}

		return null;
	}

	/**
	 * The invalid email error message.
	 *
	 * @return string the error message
	 */
	private function invalidEmailError(): string {
		return Text::get('invalidEmailError');
	}

	/**
	 * The invalid username error message.
	 *
	 * @return string the error message
	 */
	private function invalidUsernameError(): string {
		return Text::get('invalidUsernameError') . ' "a-z", "A-Z", ".", "-", "_", "@"';
	}

	/**
	 * Get the accounts array by including the accounts PHP file.
	 *
	 * @see User
	 * @return array The registered accounts
	 */
	private function load(): array {
		if (!is_readable(UserCollection::FILE_ACCOUNTS)) {
			return array();
		}

		$contents = include UserCollection::FILE_ACCOUNTS;

		/** @var string */
		$serialized = str_replace(
			$this->userTypeSerialized,
			'O:' . strlen($this->userType) . ':"' . $this->userType . '"',
			$contents
		);

		return unserialize($serialized);
	}

	/**
	 * Verify if a given email address is valid.
	 *
	 * @param string $email
	 * @return bool true in case the username is valid
	 */
	private function validEmail(string $email = ''): bool {
		preg_match('/^[a-zA-Z0-9]+[\w\.\-\_]*@[\w\.\-\_]+\.[a-zA-Z]+$/', $email, $matches);

		return (bool) $matches;
	}

	/**
	 * Verify if a given username is valid.
	 *
	 * @param string $username
	 * @return bool true in case the username is valid
	 */
	private function validUsername(string $username): bool {
		preg_match('/[^@\w\.\-]/', $username, $matches);

		return empty($matches);
	}
}
