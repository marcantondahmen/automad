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
use Automad\Core\FileSystem;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The log:filter command.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class LogFilter extends AbstractCommand {
	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->ArgumentCollection = new ArgumentCollection(array(
			new Argument('route', 'The route that is logged', true),
			new Argument('filter', 'The filter regex', true)
		));
	}

	/**
	 * Get the command description.
	 *
	 * @return string the command description
	 */
	public function description(): string {
		return 'Filter the log by regex.';
	}

	/**
	 * Get the command example.
	 *
	 * @return string the command example
	 */
	public function example(): string {
		return 'php automad/console log:filter --route /page --filter "cache page"';
	}

	/**
	 * Get the command name.
	 *
	 * @return string the command name
	 */
	public function name(): string {
		return 'log:filter';
	}

	/**
	 * The actual command action.
	 *
	 * @return int exit code
	 */
	public function run(): int {
		$route = $this->ArgumentCollection->value('route');
		$filter = preg_quote($this->ArgumentCollection->value('filter'));
		$filter = preg_replace('/\s+/s', '.*', $filter);

		$tmp = FileSystem::getTmpDir();
		$logs = "$tmp/logs";
		$log = rtrim($logs . $route, '/') . '/log.json';

		if (!is_readable($log)) {
			echo Console::clr('error', "A log file for $route was not found.") . PHP_EOL;

			return 1;
		}

		$json = file_get_contents($log);

		if (empty($json)) {
			return 1;
		}

		$data = json_decode($json, true);

		foreach ($data as $key => $value) {
			$valueJson = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

			$regex = "/$filter/is";

			if (!empty($valueJson)) {
				if (preg_match($regex, "$key$valueJson")) {
					echo Console::clr('heading', "$key: ") . PHP_EOL;
					echo Console::clr('code', $valueJson) . PHP_EOL . PHP_EOL;
				}
			}
		}

		return 0;
	}
}
