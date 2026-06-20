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

namespace Automad\System\Ai\Providers;

use Automad\Core\Debug;
use Automad\Core\Messenger;
use Automad\Core\Str;
use Automad\System\Ai\ProviderConfig;
use Automad\System\Fetch;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The abstract Ai provider class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
abstract class AbstractProvider {
	/**
	 * The provider config.
	 */
	protected ProviderConfig $ProviderConfig;

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->ProviderConfig = ProviderConfig::load($this->getId()) ?? new ProviderConfig($this->getId());
	}

	/**
	 * Generate text.
	 *
	 * @param string $prompt
	 * @param string $target
	 * @param string $context
	 * @param Messenger $Messenger
	 * @return string
	 */
	public function generate(string $prompt, string $target, string $context, Messenger $Messenger): string {
		$output = $this->requestTextApi($prompt, $target, $context, $Messenger);

		$output = Str::stripStart($output, '```html');
		$output = Str::stripEnd($output, '```');

		return trim($output);
	}

	/**
	 * The help text that is shown alongside the API key dialog on setup.
	 *
	 * @return string
	 */
	abstract public function getApiKeyHelp(): string;

	/**
	 * Get request headers.
	 *
	 * @return array
	 */
	abstract public function getHeaders(): array;

	/**
	 * Get provider id.
	 *
	 * @return string
	 */
	abstract public function getId(): string;

	/**
	 * Get publicly exposable provider info.
	 *
	 * @return array
	 */
	public function getPublicDetails(): array {
		return array(
			'id' => $this->getId(),
			'name' => $this->getName(),
			'icon' => $this->getIcon(),
			'model' => $this->ProviderConfig->model,
			'apiKeyHelp' => $this->getApiKeyHelp(),
			'website' => $this->getWebsite(),
			'isConfigured' => $this->isConfigured()
		);
	}

	/**
	 * Fetch the list of supported models by this provider.
	 *
	 * @return string[]
	 */
	abstract public function getSupportedModels(): array;

	/**
	 * Test if a provider is fully configured.
	 *
	 * @return bool
	 */
	public function isConfigured(): bool {
		return !empty($this->ProviderConfig->model) && !empty($this->ProviderConfig->apiKey);
	}

	/**
	 * Remove a provider by delting its config.
	 */
	public function remove(): void {
		$this->ProviderConfig->delete();
	}

	/**
	 * Set a provider api key.
	 *
	 * @param string $apiKey
	 * @return bool
	 */
	public function setApiKey(string $apiKey): bool {
		$this->ProviderConfig->apiKey = $apiKey;

		if (empty($apiKey)) {
			return false;
		}

		return $this->ProviderConfig->save();
	}

	/**
	 * Set a provider model.
	 *
	 * @param string $model
	 * @return bool
	 */
	public function setModel(string $model): bool {
		$this->ProviderConfig->model = $model;

		if (empty($model)) {
			return false;
		}

		return $this->ProviderConfig->save();
	}

	/**
	 * Validate the saved api key.
	 *
	 * @param string $apiKey
	 * @return bool
	 */
	abstract public function validateApiKey(string $apiKey): bool;

	/**
	 * Validate the API key that is saved in the provider config.
	 *
	 * @return bool
	 */
	public function validateSavedApiKey(): bool {
		return $this->validateApiKey($this->ProviderConfig->apiKey);
	}

	/**
	 * Validate the configured model.
	 *
	 * @return bool
	 */
	public function validateSavedModel(): bool {
		try {
			$models = $this->getSupportedModels();
			$model = $this->ProviderConfig->model;

			return in_array($model, $models);
		} catch (\Throwable $th) {
			return false;
		}
	}

	/**
	 * Compose the actual prompt that is send to the provider API.
	 *
	 * @param string $prompt
	 * @param string $target
	 * @param string $context
	 * @return string
	 */
	protected function composePrompt(string $prompt, string $target, string $context): string {
		if (empty($prompt)) {
			return '';
		}

		$prompt = <<<TEXT
			TASK:
			$prompt
			TEXT;

		$rulesArray = array();
		$rules = '';

		if (!empty($target)) {
			$rulesArray[] = 'Only modify TARGET.';
			$rulesArray[] = 'Return only the content that should be inserted or replace TARGET.';

			$target = <<<TEXT

				---

				TARGET:
				$target
				TEXT;
		}

		if (!empty($context)) {
			$rulesArray[] = 'PAGE_CONTEXT is reference only.';
			$rulesArray[] = 'Never rewrite or return PAGE_CONTEXT.';

			$context = <<<TEXT

				---

				PAGE_CONTEXT:
				$context
				TEXT;
		}

		if (!empty($rulesArray)) {
			$rules = trim(join('\n- ', $rulesArray));
			$rules = <<<TEXT
				
				---

				RULES: 
				$rules
				TEXT;
		}

		$composed = <<<TEXT
			$prompt
			$target
			$context
			$rules
			TEXT;

		Debug::log($composed, 'Composed prompt');

		return $composed;
	}

	/**
	 * The api base url.
	 *
	 * @return string
	 */
	abstract protected function getBaseUrl(): string;

	/**
	 * Get the HTML/SVG of the provider brand icon.
	 *
	 * @return string
	 */
	abstract protected function getIcon(): string;

	/**
	 * The base instructions.
	 *
	 * @return string
	 */
	protected function getInstructions(): string {
		$customInstruction = AM_AI_ASSISTANCE_INSTRUCTIONS;

		$instructions = trim(<<<TEXT
			You are a CMS text processor.

			Return only the final generated or transformed content as valid HTML markup.
			Preserve formatting.
			Preserve the original language.
			No Markdown.

			Do not:
			- explain changes
			- add introductions
			- add labels
			- add markdown
			- mention the task
			- wrap the result in quotes

			$customInstruction
			TEXT);

		Debug::log($instructions, 'Instructions');

		return $instructions;
	}

	/**
	 * Get provider name.
	 *
	 * @return string
	 */
	abstract protected function getName(): string;

	/**
	 * Get provider website.
	 *
	 * @return string
	 */
	abstract protected function getWebsite(): string;

	/**
	 * Make a provider api request.
	 *
	 * @param string $endpoint
	 * @param array|null|null $data
	 * @param Messenger $Messenger
	 * @return array
	 */
	protected function requestProviderApi(string $endpoint, array|null $data = null, Messenger|null $Messenger = null): array {
		$responseJson = Fetch::request($this->getBaseUrl() . $endpoint, $this->getHeaders(), $data);
		$response = json_decode($responseJson, true);

		if (!$response) {
			$Messenger?->setError(json_last_error_msg());
			Debug::warn($response);

			return array();
		}

		return $response;
	}

	/**
	 * Make a request to the provider's text endpoint.
	 *
	 * @param string $prompt
	 * @param string $target
	 * @param string $context
	 * @param Messenger $Messenger
	 * @return string
	 */
	abstract protected function requestTextApi(string $prompt, string $target, string $context, Messenger $Messenger): string;
}
