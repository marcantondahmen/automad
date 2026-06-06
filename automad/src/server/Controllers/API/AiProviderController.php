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
use Automad\Core\Request;
use Automad\Core\Text;
use Automad\System\Ai\ProviderCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Ai provider controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class AiProviderController {
	/**
	 * Get a list of supported models.
	 *
	 * @return Response
	 */
	public static function getModels(): Response {
		$Response = new Response();
		$id = Request::post('id');

		if (empty($id)) {
			return $Response;
		}

		$ProviderCollection = new ProviderCollection();

		$provider = $ProviderCollection->getProvider($id);

		if (!$provider) {
			return $Response;
		}

		$models = array();

		try {
			$models = $provider->getSupportedModels();
		} catch (\Throwable $th) {
		}

		if (empty($models)) {
			return $Response->setError(Text::get('aiErrorGettingModels'));
		}

		return $Response->setData($models);
	}

	/**
	 * Remove a config for a provider.
	 */
	public static function remove(): Response {
		$Response = new Response();
		$id = Request::post('id');

		if (empty($id)) {
			return $Response;
		}

		$ProviderCollection = new ProviderCollection();

		$provider = $ProviderCollection->getProvider($id);

		if (!$provider) {
			return $Response;
		}

		$provider->remove();

		return $Response;
	}

	/**
	 * Validate and set an api key in the provider config..
	 *
	 * @return Response
	 */
	public static function setApiKey(): Response {
		$Response = new Response();
		$id = Request::post('id');
		$apiKey = trim(Request::post('apiKey'));

		if (empty($id) || empty($apiKey)) {
			return $Response;
		}

		$ProviderCollection = new ProviderCollection();

		$provider = $ProviderCollection->getProvider($id);

		if (!$provider) {
			return $Response;
		}

		if (!$provider->validateApiKey($apiKey)) {
			return $Response->setError(Text::get('aiErrorInvalidProviderApiKey'));
		}

		$provider->setApiKey($apiKey);

		return $Response;
	}

	/**
	 * Set a model in the provider config.
	 *
	 * @return Response
	 */
	public static function setModel(): Response {
		$Response = new Response();
		$id = Request::post('id');
		$model = trim(Request::post('model'));

		if (empty($id) || empty($model)) {
			return $Response;
		}

		$ProviderCollection = new ProviderCollection();

		$provider = $ProviderCollection->getProvider($id);

		if (!$provider) {
			return $Response;
		}

		$provider->setModel($model);

		return $Response;
	}

	/**
	 * Validate the configured API key.
	 *
	 * @return Response
	 */
	public static function validateApiKey(): Response {
		$Response = new Response();
		$id = Request::post('id');

		if (empty($id)) {
			return $Response;
		}

		$ProviderCollection = new ProviderCollection();

		$provider = $ProviderCollection->getProvider($id);

		if (!$provider) {
			return $Response;
		}

		if (!$provider->validateSavedApiKey()) {
			return $Response->setError(Text::get('aiErrorInvalidProviderApiKey'));
		}

		return $Response->setData(array('isValid' => true));
	}

	/**
	 * Validate the configured API key.
	 *
	 * @return Response
	 */
	public static function validateModel(): Response {
		$Response = new Response();
		$id = Request::post('id');

		if (empty($id)) {
			return $Response;
		}

		$ProviderCollection = new ProviderCollection();

		$provider = $ProviderCollection->getProvider($id);

		if (!$provider) {
			return $Response;
		}

		if (!$provider->validateSavedModel()) {
			return $Response->setError(Text::get('aiErrorInvalidProviderModel'));
		}

		return $Response->setData(array('isValid' => true));
	}
}
