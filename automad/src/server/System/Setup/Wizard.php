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

use Automad\Ai\Assistance\ProviderCollection;
use Automad\Models\MailConfig;
use Automad\System\ConfigFile;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The setup wizard asks for missing settings as part of the setup.
 *
 * @see Setup
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Wizard {
	/**
	 * Get the required steps to complete the setup.
	 *
	 * @return array
	 */
	public static function getSteps(): array {
		$ProviderCollection = new ProviderCollection();
		$providers = array_filter($ProviderCollection->getPublicDetails(), fn (array $provider): bool => $provider['isConfigured']);
		$steps = array();

		if (empty(ConfigFile::read(MailConfig::CONFIG_NAME))) {
			$steps[] = 'mailConfig';
		}

		if (empty($providers) && AM_AI_ASSISTANCE_ENABLED) {
			$steps[] = 'ai';
		}

		return $steps;
	}
}
