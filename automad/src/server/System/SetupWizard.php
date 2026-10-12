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

namespace Automad\System;

use Automad\Ai\Assistance\ProviderCollection;
use Automad\Models\MailConfig;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The setup wizard class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class SetupWizard {
	/**
	 * Note that the file marker that indicates if a wizard was already completed
	 * is located inside the /automad directory in order to be removed on updates.
	 * This way, the wizard is started if needed also after updates as well as
	 * on the first login.
	 */
	const COMPLETED_FILE = AM_BASE_DIR . '/automad/.wizard-completed';

	/**
	 * Save the wizard completed file.
	 */
	public static function finish(): void {
		FileSystem::write(self::COMPLETED_FILE, '');
	}

	/**
	 * Get the required items to complete the first setup.
	 *
	 * @return array
	 */
	public static function getSteps(): array {
		$ProviderCollection = new ProviderCollection();
		$providers = array_filter($ProviderCollection->getPublicDetails(), fn (array $provider): bool => $provider['isConfigured']);
		$items = array();

		if (!is_readable(ConfigFile::getConfigPath(MailConfig::CONFIG_NAME))) {
			$items[] = 'mailConfig';
		}

		if (empty($providers) && AM_AI_ASSISTANCE_ENABLED) {
			$items[] = 'ai';
		}

		return $items;
	}

	/**
	 * This function checks for existence of the COMPLETED_FILE inside the /automad
	 * directory. This file is created when the wizard is completed and will be removed
	 * on system updates.
	 *
	 * @return bool
	 */
	public static function isCompleted(): bool {
		return is_readable(self::COMPLETED_FILE);
	}

	/**
	 * Run one-time migrations. Since the wizard is started again after every update,
	 * this is the place to migrate data that was saved by older versions.
	 * Note that legacy config files are not migrated when being read,
	 * since config files are read during Config::init(), before any permission constants are defined.
	 *
	 * @see FileSystem::migratePhpStrings()
	 */
	public static function migrate(): void {
		FileSystem::migratePhpStrings(AM_BASE_DIR . '/config');
	}
}
