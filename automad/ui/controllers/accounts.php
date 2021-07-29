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
use Automad\UI\Components\Grid\Users;
use Automad\UI\Models\Accounts as ModelsAccounts;
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
	 * @return array $output (error/success)
	 */
	public static function add() {
		$output = array();

		$username = Request::post('username');
		$password1 = Request::post('password1');
		$password2 = Request::post('password2');

		$output['error'] = ModelsAccounts::add($username, $password1, $password2);

		if (!$output['error']) {
			$output['success'] = Text::get('success_added') . ' "' . $username . '"';
		}

		return $output;
	}

	/**
	 * Optionally remove posted accounts and
	 * return the accounts grid.
	 *
	 * @return array $output
	 */
	public static function edit() {
		$output = array();

		if ($users = Request::post('delete')) {
			$output = self::delete($users);
		}

		$output['html'] = Users::render(ModelsAccounts::get());

		return $output;
	}

	/**
	 * Install the first user account.
	 *
	 * @return string Error message in case of an error.
	 */
	public static function install() {
		if (!empty($_POST)) {
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
	 * @return array $output (error/success)
	 */
	private static function delete($users) {
		$output = array();

		$output['error'] = ModelsAccounts::delete($users);

		if (!$output['error']) {
			$output['success'] = Text::get('success_remove') . ' "' . implode('", "', $users) . '"';
		}

		return $output;
	}
}
