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

namespace Automad\UI\Controllers;

use Automad\Core\Request;
use Automad\Core\Resolve;
use Automad\UI\Components\Grid\Users;
use Automad\UI\Models\Accounts as ModelsAccounts;
use Automad\UI\Response;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Accounts class provides all methods for creating and loading user accounts.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Accounts {
	/**
	 * Add user account based on $_POST.
	 *
	 * @return \Automad\UI\Response the response object
	 */
	public static function add() {
		$Response = new Response();

		$username = Request::post('username');
		$password1 = Request::post('password1');
		$password2 = Request::post('password2');

		if (!self::validUsername($username)) {
			return self::invalidUsernameResponse();
		}

		if ($error = ModelsAccounts::add($username, $password1, $password2)) {
			$Response->setError($error);

			return $Response;
		}

		$Response->setSuccess(Text::get('success_added') . ' "' . $username . '"');

		return $Response;
	}

	/**
	 * Optionally remove posted accounts and
	 * return the accounts grid.
	 *
	 * @return \Automad\UI\Response the response object
	 */
	public static function edit() {
		$Response = new Response();

		if ($users = Request::post('delete')) {
			$Response = self::delete($users);
		}

		$Response->setHtml(Users::render(ModelsAccounts::get()));

		return $Response;
	}

	/**
	 * Install the first user account.
	 *
	 * @return string Error message in case of an error.
	 */
	public static function install() {
		if (!empty($_POST)) {
			if (!self::validUsername(Request::post('username'))) {
				$Response = self::invalidUsernameResponse();

				return $Response->getError();
			}

			return ModelsAccounts::install(
				Request::post('username'),
				Request::post('password1'),
				Request::post('password2')
			);
		}
	}

	/**
	 * Verify if a password matches its hashed version.
	 *
	 * @param string $password (clear text)
	 * @param string $hash (hashed password)
	 * @return boolean true/false
	 */
	public static function passwordVerified($password, $hash) {
		return ModelsAccounts::passwordVerified($password, $hash);
	}

	/**
	 * Delete one ore more user accounts.
	 *
	 * @param array $users
	 * @return \Automad\UI\Response the response object
	 */
	private static function delete($users) {
		$Response = new Response();

		$Response->setError(ModelsAccounts::delete($users));

		if (!$Response->getError()) {
			$Response->setSuccess(Text::get('success_remove') . ' "' . implode('", "', $users) . '"');
		}

		return $Response;
	}

	/**
	 * A response containing the invalid username error message.
	 *
	 * @return \Automad\UI\Response the response object
	 */
	private static function invalidUsernameResponse() {
		$Response = new Response();
		$Response->setError(Text::get('error_invalid_username') . ' "a-z", "A-Z", ".", "-", "_", "@"');

		return $Response;
	}

	/**
	 * Verify if a given username is valid.
	 *
	 * @param string $username
	 * @return boolean true in case the username is valid
	 */
	private static function validUsername($username) {
		preg_match('/[^@\w\.\-]/', $username, $matches);

		return empty($matches);
	}
}
