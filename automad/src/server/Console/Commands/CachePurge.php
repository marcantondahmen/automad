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

namespace Automad\Console\Commands;

use Automad\Core\FileSystem;

defined('AUTOMAD_CONSOLE') or die('Console only!' . PHP_EOL);

/**
 * The purge command.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class CachePurge extends AbstractCommand {
	/**
	 * Get the command description.
	 *
	 * @return string the command description
	 */
	public function description(): string {
		return 'Purge the cache directory including all cached images and deleted pages.';
	}

	/**
	 * Get the command example.
	 *
	 * @return string the command example
	 */
	public function example(): string {
		return '';
	}

	/**
	 * Get the command name.
	 *
	 * @return string the command name
	 */
	public function name(): string {
		return 'cache:purge';
	}

	/**
	 * The actual command action.
	 *
	 * @return int exit code
	 */
	public function run(): int {
		FileSystem::purgeCache();

		return 0;
	}
}
