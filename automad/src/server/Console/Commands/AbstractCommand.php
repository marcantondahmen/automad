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
 * Copyright (c) 2021-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Console\Commands;

use Automad\Console\ArgumentCollection;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The abstract base command class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
abstract class AbstractCommand {
	/**
	 * The argument collection.
	 */
	public ArgumentCollection $ArgumentCollection;

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
	abstract public function description(): string;

	/**
	 * Get the command example.
	 *
	 * @return string the command example
	 */
	abstract public function example(): string;

	/**
	 * Get the command name.
	 *
	 * @return string the command name
	 */
	abstract public function name(): string;

	/**
	 * The actual command action.
	 *
	 * @return int exit code
	 */
	abstract public function run(): int;
}
