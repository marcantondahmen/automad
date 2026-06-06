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

use Automad\Core\FileSystem;
use Automad\Models\MailConfig;
use Automad\System\Ai\ProviderCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The setup class.
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

		if (empty($providers)) {
			$items[] = 'ai';
		}

		return $items;
	}
}
