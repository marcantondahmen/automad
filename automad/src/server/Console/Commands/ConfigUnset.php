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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Console\Commands;

use Automad\Console\Argument;
use Automad\Console\ArgumentCollection;
use Automad\Console\Console;
use Automad\Core\Config as CoreConfig;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The config-unset command.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ConfigUnset extends AbstractCommand {
	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->ArgumentCollection = new ArgumentCollection(array(
			new Argument('key', 'The configuration key', true),
		));
	}

	/**
	 * Get the command description.
	 *
	 * @return string the command description
	 */
	public function description(): string {
		return 'Unset a config value and restore the default.';
	}

	/**
	 * Get the command example.
	 *
	 * @return string the command example
	 */
	public function example(): string {
		return 'php automad/console config:unset --key AM_CACHE_ENABLED';
	}

	/**
	 * Get the command name.
	 *
	 * @return string the command name
	 */
	public function name(): string {
		return 'config:unset';
	}

	/**
	 * The actual command action.
	 *
	 * @return int exit code
	 */
	public function run(): int {
		$config = CoreConfig::read();
		$key = $this->ArgumentCollection->value('key');

		if (strlen($key) == 0) {
			echo Console::clr('error', 'The value for the --key argument must be at least one character') . PHP_EOL;

			return 1;
		}

		unset($config[$key]);
		$success = CoreConfig::write($config);

		return $success ? 0 : 1;
	}
}
