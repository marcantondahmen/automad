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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { classes, requestAPI, getPageURL, create, Routes, html } from '../core';
import { KeyValueMap } from '../types';
import { BaseComponent } from './Base';

/**
 * A breadcrumbs nav for a page.
 *
 * @example
 * <am-page-breadcrumbs></am-page-breadcrumbs>
 *
 * @extends BaseComponent
 */
class PageBreadcrumbsComponent extends BaseComponent {
	/**
	 * The constructor.
	 */
	constructor() {
		super();

		this.init();
	}

	/**
	 * Fetch the breadcrumb data and init the componenten.
	 *
	 * @async
	 */
	private async init(): Promise<void> {
		this.classList.add(classes.breadcrumbs);

		const url = getPageURL();
		const response = await requestAPI('Page/breadcrumbs', { url });

		this.render(response.data);
	}

	/**
	 * Render the actual component.
	 *
	 * @param data
	 */
	private render(data: KeyValueMap): void {
		if (!data) {
			return;
		}

		data.forEach((page: KeyValueMap, index: number) => {
			const target = `${Routes.page}?url=${encodeURIComponent(page.url)}`;

			const link = create(
				'am-link',
				[classes.breadcrumbsItem],
				{ target },
				this
			);

			if (index == data.length - 1) {
				link.setAttribute('bind', 'pageLinkUI');
				link.setAttribute('bindto', 'target');

				link.innerHTML = html`<span bind="title"></span>`;
			} else {
				link.textContent = page.title;
			}
		});
	}
}

customElements.define('am-page-breadcrumbs', PageBreadcrumbsComponent);
