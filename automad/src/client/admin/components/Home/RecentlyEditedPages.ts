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

import { BaseComponent } from '@/components/Base';
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
} from '@/core';
import { PageRecentlyEditedCardData } from '@/types';

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
		const { data } = await requestAPI(
			PageCollectionController.getRecentlyEdited
		);

		this.classList.add(CSS.grid);
		this.setAttribute('style', '--min: 12rem;');

		data.forEach((page: PageRecentlyEditedCardData) => {
			const editRoute = `${Route.page}?url=${page.url}`;
			const visitUrl = resolvePageUrl(page.url);

			create(
				'div',
				[CSS.card],
				{},
				this,
				html`
					<am-link
						${Attr.target}="${editRoute}"
						class="${CSS.cardTeaser}"
					>
						${page.thumbnail != ''
							? `<img src="${resolveFileUrl(page.thumbnail)}" />`
							: `<i class="bi bi-file-earmark-text"></i>`}
					</am-link>
					<div class="${CSS.cardTitle}">${page.title}</div>
					<div class="${CSS.cardBody}">
						<span>${dateFormat(page.lastModified)}</span>
						<span>
							${page.fileCount}
							${App.text(page.fileCount === 1 ? 'file' : 'files')}
						</span>
					</div>
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
