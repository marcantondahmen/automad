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

use Automad\System\Update as SystemUpdate;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The update command.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2018-2021 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Update extends AbstractCommand {
	/**
	 * Get the command help.
	 *
	 * @return string the command help
	 */
	public static function help() {
		return 'Update Automad to the latest version.';
	}

	/**
	 * Get the command name.
	 *
	 * @return string the command name
	 */
	public static function name() {
		return 'update';
	}

	/**
	 * The actual command action.
	 */
	public static function run() {
		if (strpos(AM_BASE_DIR, '/automad-dev') !== false) {
			exit('Can\'t run updates within the development repository!' . PHP_EOL . PHP_EOL);
		}

		echo 'Automad version ' . AM_VERSION . PHP_EOL;
		echo 'Update branch is ' . AM_UPDATE_BRANCH . PHP_EOL;

		$updateVersion = SystemUpdate::getVersion();

		if (version_compare(AM_VERSION, $updateVersion, '<')) {
			echo 'Updating to version ' . $updateVersion . PHP_EOL;
			$Response = SystemUpdate::run();

			if (!empty($Response->getCli())) {
				echo $Response->getCli() . PHP_EOL;
			} else {
				echo 'Error! Update has failed!' . PHP_EOL;
			}
		} else {
			echo 'Up to date!' . PHP_EOL;
		}
	}
}
