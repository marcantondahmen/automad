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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Console\Commands;

use Automad\Console\Argument;
use Automad\Console\ArgumentCollection;
use Automad\Console\Console;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The log:path command.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class LogPath extends AbstractCommand {
	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->ArgumentCollection = new ArgumentCollection(array(
			new Argument('help', ''),
		));
	}

	/**
	 * Get the command description.
	 *
	 * @return string the command description
	 */
	public function description(): string {
		return 'The debug log path. Can be used with <tail> as shown in help.';
	}

	/**
	 * Get the command example.
	 *
	 * @return string the command example
	 */
	public function example(): string {
		return 'php automad/console log:path';
	}

	/**
	 * Get the command name.
	 *
	 * @return string the command name
	 */
	public function name(): string {
		return 'log:path';
	}

	/**
	 * The actual command action.
	 *
	 * @return int exit code
	 */
	public function run(): int {
		if ($this->ArgumentCollection->isInArgv('help')) {
			echo Console::clr('heading', 'Logfile path: ') . PHP_EOL . Console::clr('code', AM_DEBUG_LOG_PATH) . PHP_EOL . PHP_EOL;
			echo Console::clr('text', 'You can use the following command to follow the log file on Linux or macOS:') . PHP_EOL;
			echo Console::clr('code', 'tail -F $(php automad/console log:path)') . PHP_EOL . PHP_EOL;
			echo Console::clr('text', 'You can filter the log using grep:') . PHP_EOL;
			echo Console::clr('code', 'tail -n +1 -F $(php automad/console log:path) | grep "Config"') . PHP_EOL;

			return 0;
		}

		echo AM_DEBUG_LOG_PATH . PHP_EOL;

		return 0;
	}
}
