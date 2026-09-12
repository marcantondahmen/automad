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
	App,
	Attr,
	confirm,
	create,
	createField,
	createGenericModal,
	CSS,
	dateFormat,
	FieldTag,
	findFormErrorElement,
	html,
	McpTokenController,
	notifyFormError,
	requestAPI,
} from '@/admin/core';
import { BaseComponent } from '../Base';

interface McpToken {
	id: string;
	name: string;
	createdAt: string;
}

/**
 * The MCP access token list component.
 *
 * @extends BaseComponent
 */
class McpTokenListComponent extends BaseComponent {
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
			[CSS.button],
			{},
			menu,
			App.text('systemMcpAddToken')
		);

		this.listen(addButton, 'click', this.renderAddTokenModal.bind(this));

		this.listContainer = create(
			'div',
			[CSS.grid],
			{ style: '--min: 20rem;' },
			this
		);

		this.render();
	}

	/**
	 * Render the modal used to issue a new access token.
	 */
	private renderAddTokenModal(): void {
		const nameInput = createField(FieldTag.input, null, {
			id: 'am-mcp-token-name',
			key: 'name',
			name: 'name',
			hideLabel: true,
			placeholder: App.text('systemMcpAddTokenNameLabel'),
			value: '',
		});

		const { modal, body } = createGenericModal(
			App.text('systemMcpAddTokenTitle'),
			App.text('save'),
			true,
			async (modal) => {
				const { data, error } = await requestAPI(
					McpTokenController.addToken,
					{ name: nameInput.query() }
				);

				notifyFormError(error || '', findFormErrorElement(modal));

				if (error) {
					return;
				}

				modal.close();
				this.render();
				this.renderTokenCreatedModal(data.accessToken);
			}
		);

		create('am-form-error', [], {}, body);
		body.appendChild(nameInput);

		setTimeout(() => {
			modal.open();
		}, 0);
	}

	/**
	 * Render a modal showing a newly issued access token. The raw token is only ever
	 * available here — only its hash is persisted, so it can't be shown again. A Claude
	 * Code command is shown below it purely as one convenience example, not as the only
	 * way to connect: any MCP client that supports a static Bearer token header works.
	 *
	 * @param accessToken
	 */
	private renderTokenCreatedModal(accessToken: string): void {
		const { modal, body } = createGenericModal(
			App.text('systemMcpTokenCreatedTitle'),
			App.text('close')
		);

		create('p', [], {}, body, App.text('systemMcpTokenCreatedStep1'));
		create('code', [CSS.textMono], {}, body, accessToken);

		const mcpUrl = `${window.location.origin}${App.baseIndex}/_mcp`;
		const claudeCommand = `claude mcp add --transport http automad ${mcpUrl} --header "Authorization: Bearer ${accessToken}"`;

		create(
			'span',
			[CSS.richText],
			{},
			body,
			App.text('systemMcpTokenCreatedStep2')
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
		const { data } = await requestAPI(McpTokenController.getTokens);
		const tokens = (data?.tokens ?? []) as McpToken[];

		this.listContainer.innerHTML = '';

		if (!tokens.length) {
			create(
				'p',
				[CSS.textMuted],
				{},
				this.listContainer,
				App.text('systemMcpTokensEmpty')
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
	private renderToken(token: McpToken): void {
		const card = create(
			'div',
			[CSS.card],
			{},
			this.listContainer,
			html`
				<div class="${CSS.flexItemGrow}">
					<span class="${CSS.cardIcon}"
						><i class="bi bi-key"></i
					></span>
					<div class="${CSS.cardTitle}">${token.name}</div>
					<div class="${CSS.cardBody} ${CSS.textMuted}">
						${dateFormat(token.createdAt)}
					</div>
				</div>
			`
		);

		const revoke = create(
			'span',
			[CSS.cardDelete],
			{ [Attr.tooltip]: App.text('systemMcpRevokeToken') },
			card,
			'<i class="bi bi-trash3"></i>'
		);

		this.listen(revoke, 'click', async () => {
			if (await confirm(App.text('systemMcpRevokeTokenConfirm'))) {
				await requestAPI(McpTokenController.revoke, { id: token.id });

				this.render();
			}
		});
	}
}

customElements.define('am-mcp-token-list', McpTokenListComponent);
