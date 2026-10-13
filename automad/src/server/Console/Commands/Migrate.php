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
 * See LICENSE.md for license information.
 */

namespace Automad\Console\Commands;

use Automad\Console\Console;
use Automad\System\Encryption;
use Automad\System\Setup\Migration;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The migrate command runs all setup migrations manually.
 *
 * @see Migration
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Migrate extends AbstractCommand {
	/**
	 * Get the command description.
	 *
	 * @return string the command description
	 */
	public function description(): string {
		return 'Run all setup migrations manually, for example after adding an encryption key.';
	}

	/**
	 * Get the command example.
	 *
	 * @return string the command example
	 */
	public function example(): string {
		return 'php automad/console migrate';
	}

	/**
	 * Get the command name.
	 *
	 * @return string the command name
	 */
	public function name(): string {
		return 'migrate';
	}

	/**
	 * The actual command action.
	 *
	 * @return int exit code
	 */
	public function run(): int {
		if (!Encryption::isEnabled()) {
			echo Console::clr('text', 'Encryption is disabled in this shell. Export ' . Encryption::ENV_KEY . ' or ' . Encryption::ENV_KEY_FILE) . PHP_EOL;
			echo Console::clr('text', 'in order to encrypt secrets, since SetEnv in .htaccess does not apply to the command line.') . PHP_EOL . PHP_EOL;
		}

		$failed = false;

		foreach ((new Migration())->run() as $name => $error) {
			if ($error === null) {
				echo Console::clr('code', "  [ok] $name") . PHP_EOL;

				continue;
			}

			$failed = true;
			echo Console::clr('error', "  [failed] $name: $error") . PHP_EOL;
		}

		return $failed ? 1 : 0;
	}
}
