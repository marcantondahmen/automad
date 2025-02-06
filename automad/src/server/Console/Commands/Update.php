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

use Automad\App;
use Automad\Console\Console;
use Automad\Core\Messenger;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The update command.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2018-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Update extends AbstractCommand {
	/**
	 * Get the command description.
	 *
	 * @return string the command description
	 */
	public function description(): string {
		return 'Update Automad to the latest version.';
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
		return 'update';
	}

	/**
	 * The actual command action.
	 *
	 * @return int exit code
	 */
	public function run(): int {
		if (strpos(AM_BASE_DIR, '/automad-dev') !== false) {
			echo Console::clr('error', 'Can\'t run updates within the development repository!') . PHP_EOL;

			return 1;
		}

		echo Console::clr('heading', 'Automad version ' . App::VERSION) . PHP_EOL;
		echo Console::clr('code', 'Update branch is ' . AM_UPDATE_BRANCH) . PHP_EOL;

		$updateVersion = \Automad\System\Update::getVersion();
		$Messenger = new Messenger();

		if (version_compare(App::VERSION, $updateVersion, '<')) {
			echo Console::clr('text', 'Updating to version ' . $updateVersion) . PHP_EOL;

			if (\Automad\System\Update::run($Messenger)) {
				echo $Messenger->getSuccess() . PHP_EOL;

				return 0;
			}

			echo $Messenger->getError() . PHP_EOL;
			echo Console::clr('error', 'Update has failed') . PHP_EOL;

			return 1;
		}

		echo Console::clr('text', 'Up to date') . PHP_EOL;

		return 0;
	}
}
