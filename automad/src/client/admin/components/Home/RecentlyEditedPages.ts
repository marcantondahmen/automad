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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { BaseComponent } from '@/admin/components/Base';
import {
	App,
	Attr,
	create,
	CSS,
	dateFormat,
	html,
	PageCollectionController,
	requestAPI,
	resolveFileUrl,
	resolvePageUrl,
	Route,
} from '@/admin/core';
import { PageRecentlyEditedCardData } from '@/admin/types';
import { Section } from '@/common';

/**
 * A grid of recently edited pages.
 *
 * @extends BaseComponent
 */
class RecentlyEditedPagesComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.init();
	}

	/**
	 * Request server info and render modal body.
	 */
	private async init(): Promise<void> {
		this.innerHTML = '<am-spinner></am-spinner>';

		const { data } = await requestAPI(
			PageCollectionController.getRecentlyEdited
		);

		this.innerHTML = '';

		this.classList.add(CSS.grid);
		this.setAttribute('style', '--min: 12rem;');

		data.forEach((page: PageRecentlyEditedCardData) => {
			const editRoute = `${Route.page}?url=${page.url}`;
			const visitUrl = resolvePageUrl(page.url);

			create(
				'div',
				[CSS.card, CSS.cardHover],
				{},
				this,
				html`
					<am-link
						${Attr.target}="${editRoute}&section=${Section.files}"
						class="${CSS.cardTeaser}"
					>
						${page.thumbnail != ''
							? `<img src="${resolveFileUrl(page.thumbnail)}" />`
							: `<i class="bi bi-file-earmark-text"></i>`}
					</am-link>
					<am-link
						${Attr.target}="${editRoute}"
						class="${CSS.cardTitle}"
					>
						${page.title}
					</am-link>
					<am-link
						${Attr.target}="${editRoute}"
						class="${CSS.cardBody}"
					>
						<span>${dateFormat(page.lastModified)}</span>
						<span>
							${page.fileCount}
							${App.text(page.fileCount === 1 ? 'file' : 'files')}
						</span>
					</am-link>
					<div class="${CSS.cardButtons}">
						<am-link ${Attr.target}="${editRoute}">
							<span
								class="${CSS.flex} ${CSS.flexGap} ${CSS.flexAlignCenter}"
							>
								<span>${App.text('edit')}</span>
							</span>
						</am-link>
						<a href="${visitUrl}" target="_blank">
							<span
								class="${CSS.flex} ${CSS.flexGap} ${CSS.flexAlignCenter}"
							>
								<span>${App.text('visit')}</span>
							</span>
						</a>
					</div>
				`
			);
		});
	}
}

customElements.define('am-recently-edited-pages', RecentlyEditedPagesComponent);
