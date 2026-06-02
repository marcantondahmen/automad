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

namespace Automad\System\Ai;

use Automad\System\Ai\Providers\AbstractProvider;
use Automad\System\Ai\Providers\ClaudeProvider;
use Automad\System\Ai\Providers\OpenAiProvider;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The provider collection.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class ProviderCollection {
	/**
	 * The list of providers.
	 *
	 * @var array<string, AbstractProvider>
	 */
	private array $providers;

	/**
	 * The constructor.
	 */
	public function __construct() {
		$ClaudeProvider = new ClaudeProvider();
		$OpenAiProvider = new OpenAiProvider();

		$this->providers = array(
			$ClaudeProvider->getId() => $ClaudeProvider,
			$OpenAiProvider->getId() => $OpenAiProvider
		);
	}

	/**
	 * Get a provider by its id.
	 *
	 * @param string $providerId
	 * @return AbstractProvider|null
	 */
	public function getProvider(string $providerId): AbstractProvider|null {
		return $this->providers[$providerId] ?? null;
	}

	/**
	 * Get publicly exposable details for all providers.
	 *
	 * @return array
	 */
	public function getPublicDetails(): array {
		if (!AM_AI_ASSISTANCE_ENABLED) {
			return array();
		}

		$publicDetails = array();

		foreach ($this->providers as $provider) {
			$publicDetails[] = $provider->getPublicDetails();
		};

		return $publicDetails;
	}
}
