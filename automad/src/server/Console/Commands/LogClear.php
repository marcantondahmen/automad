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

use Automad\Console\ArgumentCollection;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The log:clear command.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class LogClear extends AbstractCommand {
	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->ArgumentCollection = new ArgumentCollection(array());
	}

	/**
	 * Get the command description.
	 *
	 * @return string the command description
	 */
	public function description(): string {
		return 'Removes the debug log file.';
	}

	/**
	 * Get the command example.
	 *
	 * @return string the command example
	 */
	public function example(): string {
		return 'php automad/console log:clear';
	}

	/**
	 * Get the command name.
	 *
	 * @return string the command name
	 */
	public function name(): string {
		return 'log:clear';
	}

	/**
	 * The actual command action.
	 *
	 * @return int exit code
	 */
	public function run(): int {
		if (is_readable(AM_DEBUG_LOG_PATH)) {
			unlink(AM_DEBUG_LOG_PATH);
		}

		return 0;
	}
}
