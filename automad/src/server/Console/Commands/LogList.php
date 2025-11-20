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

use Automad\Console\ArgumentCollection;
use Automad\Console\Console;
use Automad\Core\FileSystem;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The log:list command.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class LogList extends AbstractCommand {
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
		return 'List all logged routes. This can be helpful in order to see what logs are available.';
	}

	/**
	 * Get the command example.
	 *
	 * @return string the command example
	 */
	public function example(): string {
		return 'php automad/console log:list';
	}

	/**
	 * Get the command name.
	 *
	 * @return string the command name
	 */
	public function name(): string {
		return 'log:list';
	}

	/**
	 * The actual command action.
	 *
	 * @return int exit code
	 */
	public function run(): int {
		$tmp = FileSystem::getTmpDir();
		$logDir = "$tmp/logs";

		$items = FileSystem::listDirectoryRecursively($logDir, $logDir);
		$items = array_filter($items, function ($item) {
			return preg_match('/log\.json$/', $item);
		});

		$items = array_map('dirname', $items);

		sort($items);

		if (empty($items)) {
			return 1;
		}

		foreach ($items as $item) {
			$mtime = filemtime("$logDir$item/log.json");

			if ($mtime) {
				$mtime = date('Y-m-d H:i:s', $mtime);
			} else {
				$mtime = strval($mtime);
			}

			echo Console::clr('heading', $item) . ' ' . Console::clr('version', $mtime) . PHP_EOL;
		}

		return 0;
	}
}
