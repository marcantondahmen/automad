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

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Automad;
use Automad\Core\Blocks;
use Automad\Core\Messenger;
use Automad\Core\Request;
use Automad\System\Ai\ProviderCollection;
use Automad\System\ConfigFile;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Ai controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class AiAssistanceController {
	/**
	 * Make a API request to the configured AI provide using a given prompt and context.
	 *
	 * @return Response
	 */
	public static function text(): Response {
		$Response = new Response();

		if (!AM_AI_ASSISTANCE_ENABLED) {
			return $Response;
		}

		$Messenger = new Messenger();
		$Automad = Automad::fromCache();
		$prompt = Request::post('prompt');
		$providerId = Request::post('providerId');
		$contextData = Request::post('context');
		$context = $contextData['text'] ?? '';
		$context .= Blocks::render(array('blocks' => $contextData['blocks']), $Automad);

		$ProviderCollection = new ProviderCollection();
		$provider = $ProviderCollection->getProvider($providerId);

		if (!$provider || !$prompt) {
			return $Response;
		}

		$ConfigFile = new ConfigFile();
		$ConfigFile->set('AM_AI_PROVIDER_ID', $providerId);
		$ConfigFile->write();

		$Response->setData(array('output' => $provider->requestTextApi($prompt, $context, $Messenger)));

		return $Response->setError($Messenger->getError());
	}
}
