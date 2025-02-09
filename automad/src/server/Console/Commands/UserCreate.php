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

use Automad\Console\Argument;
use Automad\Console\ArgumentCollection;
use Automad\Console\Console;
use Automad\Core\Messenger;
use Automad\Models\UserCollection;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The user:create command.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2018-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class UserCreate extends AbstractCommand {
	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->ArgumentCollection = new ArgumentCollection(array(
			new Argument('email', 'The email address'),
			new Argument('username', 'The username (defaults to a random username)'),
			new Argument('password', 'The password (defaults to a random password)')
		));
	}

	/**
	 * Get the command description.
	 *
	 * @return string the command description
	 */
	public function description(): string {
		return 'Create a new user.';
	}

	/**
	 * Get the command example.
	 *
	 * @return string the command example
	 */
	public function example(): string {
		return 'php automad/console user:create --email user@domain.com';
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

		$name = $this->ArgumentCollection->value('username');
		$name = strlen($name) ? $name : 'user_' . substr(str_shuffle(MD5(microtime())), 0, 5);

		$email = $this->ArgumentCollection->value('email');

		$password = $this->ArgumentCollection->value('password');
		$password = strlen($password) ? $password : substr(str_shuffle(MD5(microtime())), 0, 10);

		$UserCollection->createUser($name, $password, $password, $email, $Messenger);
		$UserCollection->save($Messenger);

		if (!$Messenger->getError()) {
			echo Console::clr('heading', '--------------------') . PHP_EOL;
			echo Console::clr('heading', 'Name:     ' . $name) . PHP_EOL;
			echo Console::clr('heading', 'Email:    ' . $email) . PHP_EOL;
			echo Console::clr('heading', 'Password: ' . $password) . PHP_EOL;
			echo Console::clr('heading', '--------------------') . PHP_EOL;

			return 0;
		}

		echo Console::clr('error', $Messenger->getError()) . PHP_EOL;
		echo Console::clr('error', 'Creating user account failed') . PHP_EOL;

		return 1;
	}
}
