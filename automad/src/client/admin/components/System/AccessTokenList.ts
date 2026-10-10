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
	AccessTokenController,
	App,
	Attr,
	confirm,
	create,
	createField,
	createGenericModal,
	CSS,
	dateFormat,
	EventName,
	FieldTag,
	findFormErrorElement,
	fire,
	html,
	notifyFormError,
	requestApi,
} from '@/admin/core';
import { BaseComponent } from '../Base';

interface AccessToken {
	id: string;
	name: string;
	description: string;
	preview: string;
	createdAt: string;
}

/**
 * The access token list component.
 *
 * @extends BaseComponent
 */
class AccessTokenListComponent extends BaseComponent {
	/**
	 * The list container.
	 */
	private listContainer: HTMLElement;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const menu = create('div', [CSS.flex, CSS.flexGap], {}, this);

		const addButton = create(
			'button',
			[CSS.button, CSS.buttonPrimary],
			{},
			menu,
			App.text('accessTokensAddToken')
		);

		this.listen(addButton, 'click', this.renderAddTokenModal.bind(this));

		create('p', [], {}, this, App.text('accessTokensExisting'));

		this.listContainer = create(
			'div',
			[CSS.flex, CSS.flexColumn, CSS.flexGap],
			{},
			this
		);

		this.render();
	}

	/**
	 * Render the modal used to issue a new access token.
	 */
	private renderAddTokenModal(): void {
		const nameInput = createField(FieldTag.input, null, {
			id: 'am-access-token-name',
			key: 'name',
			name: 'name',
			hideLabel: true,
			placeholder: App.text('accessTokensAddTokenNameLabel'),
			value: '',
		});

		const descriptionInput = createField(FieldTag.input, null, {
			id: 'am-access-token-description',
			key: 'description',
			name: 'description',
			hideLabel: true,
			placeholder: App.text('accessTokensAddTokenDescriptionLabel'),
			value: '',
		});

		const { modal, body } = createGenericModal(
			App.text('accessTokensAddTokenTitle'),
			App.text('save'),
			true,
			async (modal) => {
				const { data, error } = await requestApi(
					AccessTokenController.addToken,
					{
						name: nameInput.query(),
						description: descriptionInput.query(),
					}
				);

				notifyFormError(error || '', findFormErrorElement(modal));

				if (error) {
					return;
				}

				fire(EventName.appStateRequireUpdate);

				modal.close();
				this.render();
				this.renderTokenCreatedModal(data.accessToken);
			}
		);

		create('am-form-error', [], {}, body);
		body.appendChild(nameInput);
		body.appendChild(descriptionInput);

		setTimeout(() => {
			modal.open();
		}, 0);
	}

	/**
	 * Render a modal showing a newly issued access token. The raw token is only ever
	 * available here — only its hash is persisted, so it can't be shown again. A Claude
	 * Code command is shown below it purely as one example — other MCP clients that support
	 * a static Bearer token header work the same way.
	 *
	 * @param accessToken
	 */
	private renderTokenCreatedModal(accessToken: string): void {
		const { modal, body } = createGenericModal(
			App.text('accessTokensTokenCreatedTitle'),
			App.text('close')
		);

		create('p', [], {}, body, App.text('accessTokensTokenCreatedStep1'));
		create('code', [CSS.textMono], {}, body, accessToken);

		const mcpUrl = App.system.mcp.url;
		const claudeCommand = `claude mcp add --transport http automad ${mcpUrl} --header "Authorization: Bearer ${accessToken}"`;

		create(
			'span',
			[CSS.richText],
			{},
			body,
			App.text('accessTokensTokenCreatedStep2')
		);
		create('code', [CSS.textMono], {}, body, claudeCommand);

		setTimeout(() => {
			modal.open();
		}, 0);
	}

	/**
	 * Render the token list.
	 *
	 * @async
	 */
	private async render(): Promise<void> {
		const { data } = await requestApi(AccessTokenController.getTokens);
		const tokens = (data?.tokens ?? []) as AccessToken[];

		this.listContainer.innerHTML = '';

		if (!tokens.length) {
			create(
				'p',
				[CSS.textMuted],
				{},
				this.listContainer,
				App.text('accessTokensTokensEmpty')
			);

			return;
		}

		tokens.forEach((token) => {
			this.renderToken(token);
		});
	}

	/**
	 * Render a single token card.
	 *
	 * @param token
	 */
	private renderToken(token: AccessToken): void {
		const card = create(
			'div',
			[CSS.card],
			{},
			this.listContainer,
			html`
				<div class="${CSS.flexItemGrow}">
					<div class="${CSS.cardTitle}">
						<div
							class="${CSS.flex} ${CSS.flexGap} ${CSS.flexAlignCenter}"
						>
							<i class="bi bi-key"></i>
							<span>$${token.name}</span>
							<small class="${CSS.textMuted}">
								$${token.description}
							</small>
						</div>
					</div>
					<div class="${CSS.cardBody}">
						<div
							class="${CSS.flex} ${CSS.flexGap} ${CSS.flexAlignCenter}"
						>
							<span
								class="${CSS.badge} ${CSS.badgeMuted} ${CSS.textMono}"
							>
								$${token.preview}*****
							</span>
							<span class="${CSS.textMuted}">
								${dateFormat(token.createdAt)}
							</span>
						</div>
					</div>
				</div>
			`
		);

		const revoke = create(
			'span',
			[CSS.cardDelete],
			{ [Attr.tooltip]: App.text('accessTokensRevokeToken') },
			card,
			'<i class="bi bi-trash3"></i>'
		);

		this.listen(revoke, 'click', async () => {
			if (await confirm(App.text('accessTokensRevokeTokenConfirm'))) {
				await requestApi(AccessTokenController.revoke, {
					id: token.id,
				});

				fire(EventName.appStateRequireUpdate);

				this.render();
			}
		});
	}
}

customElements.define('am-access-token-list', AccessTokenListComponent);
