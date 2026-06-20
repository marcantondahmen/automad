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
use Automad\Core\Text;
use Automad\System\Fetch;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The OpenAi provider class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class OpenAiProvider extends AbstractProvider {
	/**
	 * The help text that is shown alongside the API key dialog on setup.
	 *
	 * @return string
	 */
	public function getApiKeyHelp(): string {
		return str_replace(
			array('{urlPlatform}', '{urlApiKeys}'),
			array('https://platform.openai.com/', 'https://platform.openai.com/api-keys'),
			Text::get('systemAiOpenAiSetupHelp')
		);
	}

	/**
	 * Get request headers.
	 *
	 * @return array
	 */
	public function getHeaders(): array {
		return array(
			"Authorization: Bearer {$this->ProviderConfig->apiKey}",
			'Content-Type: application/json'
		);
	}

	/**
	 * Get provider id.
	 *
	 * @return string
	 */
	public function getId(): string {
		return 'openai';
	}

	/**
	 * Fetch the list of supported models by this provider.
	 *
	 * @return string[]
	 */
	public function getSupportedModels(): array {
		$allModels = $this->requestProviderApi('/models');

		$supported = array_filter($allModels['data'], function (array $model) {
			$id = $model['id'];

			return (!preg_match('/(chat|image|audio|codex|realtime|search|transcribe|\d{4}-\d{2}-\d{2})/', $id) && str_contains($id, 'gpt-'));
		});

		usort($supported, function ($a, $b) {
			return $b['created'] <=> $a['created'];
		});

		return array_reduce($supported, function ($acc, $model) {
			$acc[] = $model['id'];

			return $acc;
		}, array());
	}

	/**
	 * Validate the saved api key.
	 *
	 * @param string $apiKey
	 * @return bool
	 */
	public function validateApiKey(string $apiKey): bool {
		$response = Fetch::request("{$this->getBaseUrl()}/models", array("Authorization: Bearer {$apiKey}"));

		if (empty($response)) {
			return false;
		}

		$data = json_decode($response);

		return empty($data->error);
	}

	/**
	 * The api base url.
	 *
	 * @return string
	 */
	protected function getBaseUrl(): string {
		return 'https://api.openai.com/v1';
	}

	/**
	 * Get the HTML/SVG of the provider brand icon.
	 *
	 * @return string
	 */
	protected function getIcon(): string {
		return '<i class="bi bi-openai"></i>';
	}

	/**
	 * Get provider name.
	 *
	 * @return string
	 */
	protected function getName(): string {
		return 'OpenAi';
	}

	/**
	 * Get provider website.
	 *
	 * @return string
	 */
	protected function getWebsite(): string {
		return 'https://openai.com';
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
	protected function requestTextApi(string $prompt, string $target, string $context, Messenger $Messenger): string {
		$response = $this->requestProviderApi(
			'/responses',
			array(
				'model' => $this->ProviderConfig->model,
				'instructions' => $this->getInstructions(),
				'input' => $this->composePrompt($prompt, $target, $context)
			),
			$Messenger
		);

		if (!empty($response['error'])) {
			$Messenger->setError($response['error']);
			Debug::warn($response);

			return '';
		}

		if (empty($response['output'])) {
			return '';
		}

		$text = array();

		foreach ($response['output'] ?? array() as $output) {
			if (($output['type'] ?? null) !== 'message') {
				continue;
			}

			foreach ($output['content'] ?? array() as $content) {
				if (($content['type'] ?? null) === 'output_text') {
					$text[] = $content['text'] ?? '';
				}
			}
		}

		Debug::log($response, 'OpenAI response');

		return implode("\n", $text);
	}
}
