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
 * Copyright (c) 2018-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Commands;

use Automad\Types\User;
use Automad\UI\Models\UserCollectionModel;
use Automad\UI\Utils\Messenger;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The createuser command.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2018-2021 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class CreateUser extends AbstractCommand {
	/**
	 * Get the command help.
	 *
	 * @return string the command help
	 */
	public static function help() {
		return 'Create a new user with a random name and password.';
	}

	/**
	 * Get the command name.
	 *
	 * @return string the command name
	 */
	public static function name() {
		return 'createuser';
	}

	/**
	 * The actual command action.
	 */
	public static function run() {
		echo 'Creating new user account for the Automad dashboard ...' . PHP_EOL . PHP_EOL;

		$UserCollectionModel = new UserCollectionModel();
		$Messenger = new Messenger();

		$name = 'user_' . substr(str_shuffle(MD5(microtime())), 0, 5);
		$password = substr(str_shuffle(MD5(microtime())), 0, 10);

		$UserCollectionModel->createUser($name, $password, $password, '', $Messenger);
		$UserCollectionModel->save($Messenger);

		if (!$Messenger->getError()) {
			echo '--------------------' . PHP_EOL;
			echo 'Name:     ' . $name . PHP_EOL;
			echo 'Password: ' . $password . PHP_EOL;
			echo '--------------------' . PHP_EOL;
		} else {
			echo 'Error! Creating of user account failed.' . PHP_EOL;
		}
	}
}
