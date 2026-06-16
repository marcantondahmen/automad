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
 * The Claude AI provider class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class ClaudeProvider extends AbstractProvider {
	/**
	 * The help text that is shown alongside the API key dialog on setup.
	 *
	 * @return string
	 */
	public function getApiKeyHelp(): string {
		return str_replace(
			array('{urlPlatform}', '{urlApiKeys}'),
			array('https://platform.claude.com/', 'https://platform.claude.com/settings/workspaces/default/keys'),
			Text::get('systemAiClaudeSetupHelp')
		);
	}

	/**
	 * Get request headers.
	 *
	 * @return array
	 */
	public function getHeaders(): array {
		return array(
			'Content-Type: application/json',
			'anthropic-version: 2023-06-01',
			"X-Api-Key: {$this->ProviderConfig->apiKey}"
		);
	}

	/**
	 * Get provider id.
	 *
	 * @return string
	 */
	public function getId(): string {
		return 'claude';
	}

	/**
	 * Fetch the list of supported models by this provider.
	 *
	 * @return string[]
	 */
	public function getSupportedModels(): array {
		$response = $this->requestProviderApi('/models');

		if (empty($response['data'])) {
			return array();
		}

		$models = $response['data'];

		usort($models, function ($a, $b) {
			return $b['created_at'] <=> $a['created_at'];
		});

		return array_reduce($models, function ($acc, $model) {
			$acc[] = $model['id'];

			return $acc;
		}, array());
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
	public function requestTextApi(string $prompt, string $target, string $context, Messenger $Messenger): string {
		$response = $this->requestProviderApi(
			'/messages',
			array(
				'max_tokens' => 2048,
				'model' => $this->ProviderConfig->model,
				'system' => array(array('text' => $this->getInstructions(), 'type' => 'text')),
				'thinking' => array('type' => 'disabled'),
				'messages' => array(
					array(
						'content' => $this->composePrompt($prompt, $target, $context),
						'role' => 'user'
					)
				)
			),
			$Messenger
		);

		if (!empty($response['error'])) {
			$Messenger->setError($response['error']);
			Debug::warn($response);

			return '';
		}

		if (empty($response['content'])) {
			return '';
		}

		$text = array();

		foreach ($response['content'] ?? array() as $content) {
			if (($content['type'] ?? null) === 'text') {
				$text[] = $content['text'] ?? '';
			}
		}

		Debug::log($response, 'Claude response');

		return implode("\n", $text);
	}

	/**
	 * Validate the saved api key.
	 *
	 * @param string $apiKey
	 * @return bool
	 */
	public function validateApiKey(string $apiKey): bool {
		$response = Fetch::request(
			"{$this->getBaseUrl()}/models",
			array(
				"X-Api-Key: {$apiKey}",
				'Content-Type: application/json',
				'anthropic-version: 2023-06-01',
			)
		);

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
		return 'https://api.anthropic.com/v1';
	}

	/**
	 * Get the HTML/SVG of the provider brand icon.
	 *
	 * @return string
	 */
	protected function getIcon(): string {
		return '<i class="bi bi-claude"></i>';
	}

	/**
	 * Get provider name.
	 *
	 * @return string
	 */
	protected function getName(): string {
		return 'Claude';
	}

	/**
	 * Get provider website.
	 *
	 * @return string
	 */
	protected function getWebsite(): string {
		return 'https://www.anthropic.com';
	}
}
