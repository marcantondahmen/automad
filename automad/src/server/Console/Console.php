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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Console;

use Automad\App;
use Automad\Console\Commands\AbstractCommand;
use Automad\Core\FileSystem;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The console class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Console {
	/**
	 * Colors.
	 */
	const COLORS = array(
		'code' => 32,
		'title' => 90,
		'heading' => 33,
		'text' => 37,
		'arg' => 34,
		'version' => 90,
		'error' => 91
	);

	/**
	 * The console constructor.
	 *
	 * @param array $argv
	 */
	public function __construct(array $argv) {
		$this->run($argv);
		echo PHP_EOL;
	}

	/**
	 * Colorize a string.
	 *
	 * @param string $color
	 * @param string $str
	 * @return string
	 */
	public static function clr(string $color, string $str): string {
		$code = self::COLORS[$color] ?? 37;

		return "\033[{$code}m{$str}\033[0m";
	}

	/**
	 * Get the list of available commands.
	 *
	 * @return AbstractCommand[] the list of command objects
	 */
	private function getCommands(): array {
		$files = FileSystem::glob(AM_BASE_DIR . '/automad/src/server/Console/Commands/*.php');

		foreach ($files as $file) {
			require_once $file;
		}

		$classList = array_filter(get_declared_classes(), function ($cls) {
			return (strpos($cls, 'Automad\Console\Commands') !== false && strpos($cls, 'Commands\AbstractCommand') === false);
		});

		$commands = array();

		foreach ($classList as $cls) {
			$command = new $cls();
			$commands[$command->name()] = $command;
		}

		/** @var AbstractCommand[] */
		return $commands;
	}

	/**
	 * Show the help for all available commands.
	 *
	 * @param AbstractCommand[] $commands
	 */
	private function help(array $commands): void {
		echo PHP_EOL . self::clr('heading', 'Commands: ') . PHP_EOL;

		foreach ($commands as $name => $command) {
			echo '    ' . self::clr('code', str_pad($name, 15)) . self::clr('text', $command->description()) . PHP_EOL;

			$args = $command->ArgumentCollection->args;

			if (count($args)) {
				echo PHP_EOL . str_pad(' ', 19, ' ', STR_PAD_LEFT) . self::clr('heading', 'Arguments:') . PHP_EOL;
			}

			foreach ($args as $Argument) {
				echo str_pad(' ', 19, ' ', STR_PAD_LEFT);
				echo self::clr('arg', str_pad('--' . $Argument->name, 10, ' ')) . '  ';
				echo self::clr('text', ($Argument->required ? '' : '[optional] ') . $Argument->description) . PHP_EOL;
			}

			echo PHP_EOL;

			if ($example = $command->example()) {
				echo str_pad(' ', 19, ' ', STR_PAD_LEFT) . self::clr('heading', 'Example: ') . PHP_EOL;
				echo str_pad(' ', 19, ' ', STR_PAD_LEFT) . self::clr('code', $example) . PHP_EOL . PHP_EOL;
			}
		}
	}

	/**
	 * Run a command based on the $argv array.
	 *
	 * @param array $argv
	 */
	private function run(array $argv): void {
		$commands = $this->getCommands();

		if (empty($argv[1])) {
			echo self::clr(
				'title',
				<<<ASCII

				               _                            _ 
				    /\        | |                          | |
				   /  \  _   _| |_ ___  _ __ ___   __ _  __| |
				  / /\ \| | | | __/ _ \| '_ ` _ \ / _` |/ _` |
				 / ____ \ |_| | || (_) | | | | | | (_| | (_| |
				/_/    \_\__,_|\__\___/|_| |_| |_|\__,_|\__,_|

				ASCII . PHP_EOL
			);

			echo self::clr('version', 'Automad CLI version ' . App::VERSION) . PHP_EOL . PHP_EOL;
			echo self::clr('heading', 'Usage:') . PHP_EOL;
			echo self::clr('code', '    php automad/console command [--arg value ...] ') . PHP_EOL;
			$this->help($commands);
			exit(0);
		}

		$name = $argv[1];

		if (array_key_exists($name, $commands)) {
			$command = $commands[$name];

			if (!$command->ArgumentCollection->parseArgv($argv)) {
				exit(1);
			}

			exit($command->run());
		}

		echo self::clr('error', "The command [$name] does not exist.") . PHP_EOL;
		$this->help($commands);
		exit(1);
	}
}
