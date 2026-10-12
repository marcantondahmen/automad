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

namespace Automad\System\Setup;

use Automad\System\FileSystem;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The setup is the one-time process that runs after a fresh install and after every update.
 * It consists of the migration of data that was saved by older versions and the interactive wizard
 * that asks for missing settings.
 *
 * Note that the marker file that indicates whether the setup was already completed
 * is located inside the /automad directory in order to be removed on updates.
 * This way, the setup is started on the first login as well as after every update.
 * Also note that legacy config files are not migrated when being read,
 * since config files are read during Config::init(), before any permission constants are defined.
 *
 * @see Migration
 * @see Wizard
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Setup {
	const COMPLETED_FILE = AM_BASE_DIR . '/automad/.setup-completed';

	/**
	 * Save the setup completed file.
	 */
	public static function finish(): void {
		FileSystem::write(self::COMPLETED_FILE, '');
	}

	/**
	 * This function checks for existence of the COMPLETED_FILE inside the /automad
	 * directory. This file is created when the setup is completed and will be removed
	 * on system updates.
	 *
	 * @return bool
	 */
	public static function isCompleted(): bool {
		return is_readable(self::COMPLETED_FILE);
	}

	/**
	 * Start the setup by running all migrations and return the required wizard steps.
	 *
	 * @return array the required wizard steps
	 */
	public static function start(): array {
		(new Migration())->run();

		return Wizard::getSteps();
	}
}
