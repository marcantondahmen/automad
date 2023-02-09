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
 * Copyright (c) 2018-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Console\Commands;

use Automad\Core\Messenger;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The update command.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2018-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Update extends AbstractCommand {
	/**
	 * Get the command help.
	 *
	 * @return string the command help
	 */
	public static function help(): string {
		return 'Update Automad to the latest version.';
	}

	/**
	 * Get the command name.
	 *
	 * @return string the command name
	 */
	public static function name(): string {
		return 'update';
	}

	/**
	 * The actual command action.
	 */
	public static function run(): void {
		if (strpos(AM_BASE_DIR, '/automad-dev') !== false) {
			exit('Can\'t run updates within the development repository!' . PHP_EOL . PHP_EOL);
		}

		echo 'Automad version ' . AM_VERSION . PHP_EOL;
		echo 'Update branch is ' . AM_UPDATE_BRANCH . PHP_EOL;

		$updateVersion = \Automad\System\Update::getVersion();
		$Messenger = new Messenger();

		if (version_compare(AM_VERSION, $updateVersion, '<')) {
			echo 'Updating to version ' . $updateVersion . PHP_EOL;

			if (\Automad\System\Update::run($Messenger)) {
				echo $Messenger->getSuccess() . PHP_EOL;

				exit(0);
			}

			echo $Messenger->getError() . PHP_EOL;
			echo 'Update has failed' . PHP_EOL;

			exit(1);
		}

		echo 'Up to date' . PHP_EOL;
	}
}
