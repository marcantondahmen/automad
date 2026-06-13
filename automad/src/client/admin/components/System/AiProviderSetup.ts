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

import {
	AiProviderController,
	App,
	Attr,
	confirm,
	create,
	createField,
	createGenericModal,
	createSelect,
	CSS,
	debounce,
	EventName,
	FieldTag,
	findFormErrorElement,
	fire,
	html,
	notifyFormError,
	query,
	requestAPI,
} from '@/admin/core';
import { BaseComponent } from '../Base';
import { AiProvider, APIResponse } from '@/admin/types';

const modelDisplayName = (model: string): string =>
	model
		.replace(/(\d)-(\d)/g, '$1.$2')
		.replace(/-/g, ' ')
		.split(' ')
		.map((word) => word.charAt(0).toUpperCase() + word.slice(1))
		.join(' ');

/**
 * The AI provider setup component.
 *
 * @extends BaseComponent
 */
class AiProviderSetupComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.grid);
		this.setAttribute('style', '--min: 17rem;');
		this.render();

		this.listen(window, EventName.appStateChange, this.render.bind(this));
	}

	/**
	 * Render all provider cards.
	 */
	private render(): void {
		if (!App.system.ai.enabled) {
			return;
		}

		const providers = App.system.ai.providers;

		this.innerHTML = '';

		providers.forEach((provider: AiProvider) => {
			this.renderProvider(provider);
		});
	}

	/**
	 * Render a single provider card.
	 *
	 * @param provider
	 */
	private renderProvider(provider: AiProvider): void {
		if (provider.isConfigured) {
			this.renderConfigCard(provider);

			return;
		}

		this.renderSetupCard(provider);
	}

	/**
	 * Render the card header for a given provider.
	 *
	 * @param provider
	 * @return the rendered header
	 */
	private renderCardHeader(provider: AiProvider): string {
		return html`
			<span class="${CSS.cardIcon}">${provider.icon}</span>
			<div class="${CSS.cardTitle}">${provider.name}</div>
			<div class="${CSS.cardBody}">
				<a
					href="${provider.website}"
					class="${CSS.textParagraph}"
					target="_blank"
				>
					${provider.website}
				</a>
			</div>
		`;
	}

	/**
	 * Render the card for a non-configured provider that starts the setup.
	 *
	 * @param provider
	 */
	private renderSetupCard(provider: AiProvider): void {
		const card = create(
			'div',
			[CSS.card],
			{},
			this,
			this.renderCardHeader(provider)
		);

		const buttons = create('div', [CSS.cardButtons], {}, card);

		const button = create(
			'span',
			[],
			{},
			buttons,
			App.text('systemAiProviderSetup')
		);

		this.listen(
			button,
			'click',
			this.renderApiKeyModal.bind(
				this,
				provider,
				this.renderModelSelectModal.bind(this, provider)
			)
		);
	}

	/**
	 * Render the config card.
	 *
	 * @param provider
	 */
	private renderConfigCard(provider: AiProvider): void {
		const card = create(
			'div',
			[CSS.card],
			{},
			this,
			this.renderCardHeader(provider)
		);

		const buttons = create('div', [CSS.cardButtons], {}, card);

		const removeButton = create(
			'span',
			[CSS.cardDelete],
			{ [Attr.tooltip]: App.text('systemAiProviderRemove') },
			card,
			'<i class="bi bi-trash3"></i>'
		);

		const changeModelButton = create(
			'span',
			[CSS.flex, CSS.flexGap],
			{},
			buttons,
			html`
				<span>${modelDisplayName(provider.model)}</span>
				<am-ai-model-validation-indicator
					${Attr.aiProviderId}="${provider.id}"
				></am-ai-model-validation-indicator>
			`
		);

		const setNewApiKeyButton = create(
			'span',
			[CSS.flex, CSS.flexGap],
			{},
			buttons,
			html`
				<span>${App.text('systemAiChangeApiKey')}</span>
				<am-ai-api-key-validation-indicator
					${Attr.aiProviderId}="${provider.id}"
				></am-ai-api-key-validation-indicator>
			`
		);

		this.listen(
			removeButton,
			'click',
			this.removeConfig.bind(this, provider)
		);

		this.listen(
			setNewApiKeyButton,
			'click',
			this.renderApiKeyModal.bind(this, provider)
		);

		this.listen(
			changeModelButton,
			'click',
			this.renderModelSelectModal.bind(this, provider)
		);
	}

	/**
	 * Render the modal for API key configuration.
	 *
	 * @param provider
	 * @param [onSuccess]
	 */
	private renderApiKeyModal(
		provider: AiProvider,
		onSuccess: () => void = () => {}
	): void {
		const apiKeyInput = createField(FieldTag.input, null, {
			id: 'am-ai-api-key',
			key: 'apiKey',
			name: 'apiKey',
			hideLabel: true,
			placeholder: 'API key',
			value: '',
		});

		const { modal, body, button } = createGenericModal(
			App.text('systemAiProviderApiKeyModalTitle'),
			App.text('systemAiProviderApiKeyModalButton'),
			true,
			async (modal) => {
				const { error } = await this.setApiKey(
					provider,
					apiKeyInput.query()
				);

				notifyFormError(error || '', findFormErrorElement(modal));

				if (error) {
					return;
				}

				modal.close();

				onSuccess();
			}
		);

		create('am-form-error', [], {}, body);
		create('span', [], {}, body, provider.apiKeyHelp);
		body.appendChild(apiKeyInput);
		button.disabled = true;

		this.listen(
			apiKeyInput,
			'input',
			debounce(() => {
				button.disabled = !apiKeyInput.query();
			})
		);

		setTimeout(() => {
			modal.open();
		}, 0);
	}

	/**
	 * Render the modal dialog for selecting a model.
	 *
	 * @param provider
	 */
	private renderModelSelectModal(provider: AiProvider): void {
		const { modal, body } = createGenericModal(
			App.text('systemAiProviderModelModalTitle'),
			App.text('save'),
			true,
			async (modal) => {
				const select = query<HTMLSelectElement>('select', modal);

				if (!select) {
					return;
				}

				const { error } = await this.setModel(provider, select.value);

				if (error) {
					notifyFormError(error || '', findFormErrorElement(modal));

					return;
				}

				modal.close();
			}
		);

		create('am-form-error', [], {}, body);
		create(
			'span',
			[],
			{},
			body,
			App.text('systemAiProviderModelModalHelp')
		);

		const field = create('div', [CSS.field], {}, body);

		setTimeout(async () => {
			modal.open();

			const spinner = create('am-spinner', [], {}, body);

			const { data: models, error } = await requestAPI(
				AiProviderController.getModels,
				{
					id: provider.id,
				}
			);

			notifyFormError(error || '', findFormErrorElement(modal));

			if (error) {
				return;
			}

			const select = createSelect(
				models.map((value: string) => ({
					value,
					text: modelDisplayName(value),
				})),
				provider.model || models[0]
			);

			spinner.remove();

			field.appendChild(select);
		}, 0);
	}

	/**
	 * Send the API key to the backend controller.
	 *
	 * @param provider
	 * @param apiKey
	 * @return the api response
	 * @async
	 */
	private async setApiKey(
		provider: AiProvider,
		apiKey: string
	): Promise<APIResponse> {
		const response = await requestAPI(AiProviderController.setApiKey, {
			id: provider.id,
			apiKey,
		});

		fire(EventName.appStateRequireUpdate, window);

		return response;
	}

	/**
	 * Send the selected model to the backend controller.
	 *
	 * @param provider
	 * @param model
	 * @return the api response
	 * @async
	 */
	private async setModel(
		provider: AiProvider,
		model: string
	): Promise<APIResponse> {
		const response = await requestAPI(AiProviderController.setModel, {
			id: provider.id,
			model,
		});

		fire(EventName.appStateRequireUpdate, window);

		return response;
	}

	/**
	 * Remove a provider configuration.
	 *
	 * @param provider
	 * @async
	 */
	private async removeConfig(provider: AiProvider): Promise<void> {
		if (await confirm(App.text('systemAiProviderRemoveConfirm'))) {
			await requestAPI(AiProviderController.remove, { id: provider.id });

			fire(EventName.appStateRequireUpdate, window);
		}
	}
}

customElements.define('am-ai-provider-setup', AiProviderSetupComponent);
