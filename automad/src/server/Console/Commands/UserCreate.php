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
 * Copyright (c) 2018-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Console\Commands;

use Automad\Console\Console;
use Automad\Core\Messenger;
use Automad\Models\UserCollection;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The createuser command.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2018-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class CreateUser extends AbstractCommand {
	/**
	 * Get the command description.
	 *
	 * @return string the command description
	 */
	public function description(): string {
		return 'Create a new user with a random name and password.';
	}

	/**
	 * Get the command example.
	 *
	 * @return string the command example
	 */
	public function example(): string {
		return '';
	}

	/**
	 * Get the command name.
	 *
	 * @return string the command name
	 */
	public function name(): string {
		return 'user:create';
	}

	/**
	 * The actual command action.
	 *
	 * @return int exit code
	 */
	public function run(): int {
		echo Console::clr('text', 'Creating new user account for the Automad dashboard ...') . PHP_EOL . PHP_EOL;

		$UserCollection = new UserCollection();
		$Messenger = new Messenger();

		$name = 'user_' . substr(str_shuffle(MD5(microtime())), 0, 5);
		$password = substr(str_shuffle(MD5(microtime())), 0, 10);

		$UserCollection->createUser($name, $password, $password, '', $Messenger);
		$UserCollection->save($Messenger);

		if (!$Messenger->getError()) {
			echo Console::clr('heading', '--------------------') . PHP_EOL;
			echo Console::clr('heading', 'Name:     ' . $name) . PHP_EOL;
			echo Console::clr('heading', 'Password: ' . $password) . PHP_EOL;
			echo Console::clr('heading', '--------------------') . PHP_EOL;

			return 0;
		}

		echo $Messenger->getError() . PHP_EOL;
		echo Console::clr('error', 'Creating user account failed') . PHP_EOL;

		return 1;
	}
}
