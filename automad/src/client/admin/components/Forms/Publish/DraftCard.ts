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

import { BaseComponent } from '@/admin/components/Base';
import {
	App,
	Attr,
	create,
	CSS,
	dateFormat,
	EventName,
	html,
	PageController,
	routes,
} from '@/admin/core';
import type { FormComponent } from '@/admin/components/Forms/Form';
import { Draft } from './types';

/**
 * A card representing a single page with unpublished draft changes.
 *
 * @extends BaseComponent
 */
export class DraftCardComponent extends BaseComponent {
	/**
	 * The tag name of the component.
	 */
	static TAG_NAME = 'am-draft-card';

	set data(draft: Draft) {
		this.render(draft);
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.card);
	}

	/**
	 * Render the card with given data.
	 *
	 * @param draft
	 */
	private render(draft: Draft): void {
		this.innerHTML = html`
			<am-link
				${Attr.target}="${routes.page}?url=${draft.url}"
				class="${CSS.cardIcon}"
			>
				<i class="bi bi-file-earmark-post"></i>
			</am-link>
			<am-link
				${Attr.target}="${routes.page}?url=${draft.url}"
				class="${CSS.cardTitle}"
			>
				${draft.title}
			</am-link>
			<div class="${CSS.cardBody}">${dateFormat(draft.lastModified)}</div>
			<div class="${CSS.cardButtons}"></div>
		`;

		const buttons = this.querySelector(
			`.${CSS.cardButtons}`
		) as HTMLElement;

		const publishForm = create<FormComponent>(
			'am-form',
			[],
			{
				[Attr.api]: PageController.publish,
				[Attr.event]: EventName.appStateRequireUpdate,
			},
			buttons
		);

		publishForm.additionalData = { url: draft.url };

		create('am-submit', [], {}, publishForm, App.text('publish'));

		if (draft.lastPublished) {
			const discardForm = create<FormComponent>(
				'am-form',
				[],
				{
					[Attr.api]: PageController.discardDraft,
					[Attr.confirm]: App.text('discardDraftConfirm'),
					[Attr.event]: EventName.appStateRequireUpdate,
				},
				buttons
			);

			discardForm.additionalData = { url: draft.url };
			create('am-submit', [], {}, discardForm, App.text('discard'));
		}
	}
}

customElements.define(DraftCardComponent.TAG_NAME, DraftCardComponent);
