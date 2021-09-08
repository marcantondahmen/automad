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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI;

use Automad\UI\Utils\FileSystem;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The console class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Console {
	/**
	 * The console constructor.
	 *
	 * @param array $argv
	 */
	public function __construct(array $argv) {
		echo 'Automad Console version ' . AM_VERSION . PHP_EOL . PHP_EOL;
		$this->runCommand($argv);
		echo PHP_EOL;
	}

	/**
	 * Get the list of available commands.
	 *
	 * @return array the list of command objects
	 */
	private function getCommands() {
		$files = FileSystem::glob(AM_BASE_DIR . '/automad/src/UI/Commands/*.php');

		foreach ($files as $file) {
			require_once $file;
		}

		$classList = array_filter(get_declared_classes(), function ($cls) {
			return (strpos($cls, 'Automad\UI\Commands') !== false && strpos($cls, 'Commands\AbstractCommand') === false);
		});

		$commands = array();

		foreach ($classList as $cls) {
			$command = new $cls();
			$commands[$command->name()] = (object) array(
				'class' => $cls,
				'help' => $command->help()
			);
		}

		return $commands;
	}

	/**
	 * Show the help for all available commands.
	 *
	 * @param array $commands
	 */
	private function help(array $commands) {
		echo PHP_EOL . 'Commands: ' . PHP_EOL;

		foreach ($commands as $name => $command) {
			echo '    ' . str_pad($name, 15) . $command->help . PHP_EOL;
		}
	}

	/**
	 * Run a command based on the $argv array.
	 *
	 * @param array $argv
	 */
	private function runCommand(array $argv) {
		$commands = $this->getCommands();

		if (empty($argv[1])) {
			echo 'Usage:' . PHP_EOL;
			echo '    php automad/console [command]' . PHP_EOL;
			$this->help($commands);
		} else {
			$name = $argv[1];

			if (array_key_exists($name, $commands)) {
				$cls = $commands[$name]->class;
				$command = new $cls;
				$command->run();
			} else {
				echo "The command $name does not exist." . PHP_EOL;
				$this->help($commands);
			}
		}
	}
}
