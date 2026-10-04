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
	DraftCollectionController,
	EventName,
	html,
	requestApi,
} from '@/admin/core';
import { DraftCardComponent } from './DraftCard';
import { Draft } from './types';

/**
 * A grid of pages that currently have unpublished draft changes.
 *
 * @extends BaseComponent
 */
class DraftListComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.init();

		this.listen(window, EventName.appStateChange, this.init.bind(this));
	}

	/**
	 * Render the intro paragraph and the grid of draft cards from the current app state.
	 *
	 * @async
	 */
	private async init(): Promise<void> {
		const { data } = await requestApi(DraftCollectionController.get);
		const drafts = (data?.drafts || []) as Draft[];
		const hasDrafts = !!drafts?.length;

		drafts.sort((a, b) => b.lastModified.localeCompare(a.lastModified));

		this.innerHTML = hasDrafts
			? html` <p>${App.text('draftsHint')}</p> `
			: html`<am-alert
					${Attr.icon}="check-circle"
					${Attr.text}="draftsHintEmpty"
				></am-alert> `;

		if (!hasDrafts) {
			return;
		}

		const grid = create(
			'div',
			[CSS.grid],
			{ style: '--min: 16rem;' },
			this
		);

		drafts.forEach((draft) => {
			const card = create(DraftCardComponent.TAG_NAME, [], {}, grid);

			card.data = draft;
		});
	}
}

customElements.define('am-draft-list', DraftListComponent);
