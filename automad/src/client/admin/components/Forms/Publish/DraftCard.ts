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
	DraftCollectionController,
	EventName,
	html,
	routes,
} from '@/admin/core';
import type { FormComponent } from '@/admin/components/Forms/Form';
import { Draft, DraftType } from './types';

const icons: { [key in DraftType]: string } = {
	page: 'file-earmark-post',
	shared: 'asterisk',
	components: 'boxes',
} as const;

const getTarget: { [key in DraftType]: (draft: Draft) => string } = {
	page: (draft) => `${routes.page}?url=${draft.url}`,
	shared: () => routes.shared,
	components: () => routes.components,
} as const;

const getTitle: { [key in DraftType]: (draft: Draft) => string } = {
	page: (draft) => draft.title,
	shared: () => App.text('sharedTitle'),
	components: () => App.text('componentsTitle'),
} as const;

const publishController: { [key in DraftType]: DraftCollectionController } = {
	page: DraftCollectionController.publishPage,
	shared: DraftCollectionController.publishShared,
	components: DraftCollectionController.publishComponents,
} as const;

const discardController: { [key in DraftType]: DraftCollectionController } = {
	page: DraftCollectionController.discardPage,
	shared: DraftCollectionController.discardShared,
	components: DraftCollectionController.discardComponents,
} as const;

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

	/**
	 * Set data to initialize and render.
	 */
	set data(draft: Draft) {
		this.render(draft);
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.card, CSS.cardHover);
	}

	/**
	 * Render the card with given data.
	 *
	 * @param draft
	 */
	private render(draft: Draft): void {
		const icon = icons[draft.type];
		const target = getTarget[draft.type](draft);
		const title = getTitle[draft.type](draft);

		this.innerHTML = html`
			<am-link ${Attr.target}="${target}" title="${draft.title}">
				<div
					class="${CSS.cardIcon} ${draft.type == 'page'
						? CSS.cardIconNarrow
						: ''}"
				>
					<i class="bi bi-${icon}"></i>
				</div>
				<div class="${CSS.cardTitle}">${title}</div>
				<div class="${CSS.cardBody}">
					${draft.lastModified ? dateFormat(draft.lastModified) : ''}
				</div>
			</am-link>
			<div class="${CSS.cardButtons}"></div>
		`;

		const buttons = this.querySelector(
			`.${CSS.cardButtons}`
		) as HTMLElement;

		const publishForm = create<FormComponent>(
			'am-form',
			[],
			{
				[Attr.api]: publishController[draft.type],
				[Attr.event]: EventName.appStateRequireUpdate,
			},
			buttons
		);

		publishForm.additionalData = { url: draft.url };

		create('am-submit', [], {}, publishForm, App.text('publish'));

		const discardForm = create<FormComponent>(
			'am-form',
			[],
			{
				[Attr.api]: discardController[draft.type],
				[Attr.confirm]: App.text('discardDraftConfirm'),
				[Attr.event]: EventName.appStateRequireUpdate,
			},
			buttons
		);

		discardForm.additionalData = { url: draft.url };

		create('am-submit', [], {}, discardForm, App.text('discard'));
	}
}

customElements.define(DraftCardComponent.TAG_NAME, DraftCardComponent);
