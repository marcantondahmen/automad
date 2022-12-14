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
 * Copyright (c) 2021-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	requestAPI,
	getPageURL,
	create,
	Route,
	html,
	CSS,
	App,
	Attr,
} from '../core';
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
		this.classList.add(CSS.breadcrumbs);

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
		const dashboard = create(
			'am-link',
			[CSS.breadcrumbsItem],
			{ [Attr.target]: Route.home },
			this
		);

		dashboard.textContent = App.text('dashboardTitle');

		if (!data) {
			return;
		}

		data.forEach((page: KeyValueMap, index: number) => {
			const target = `${Route.page}?url=${encodeURIComponent(page.url)}`;

			const link = create(
				'am-link',
				[CSS.breadcrumbsItem],
				{ [Attr.target]: target },
				this
			);

			if (index == data.length - 1) {
				link.setAttribute(Attr.bind, 'pageLinkUI');
				link.setAttribute(Attr.bindTo, Attr.target);

				link.innerHTML = html`<span ${Attr.bind}="title">
					$${page.title}
				</span>`;
			} else {
				link.textContent = page.title;
			}
		});
	}
}

customElements.define('am-page-breadcrumbs', PageBreadcrumbsComponent);
