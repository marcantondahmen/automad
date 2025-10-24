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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
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
	PageController,
} from '@/admin/core';
import { KeyValueMap } from '@/admin/types';
import { BaseBreadcrumbsComponent } from './BaseBreadcrumbs';

/**
 * A breadcrumbs nav for a page.
 *
 * @example
 * <am-breadcrumbs-page></am-breadcrumbs-page>
 *
 * @extends BaseBreadcrumbsComponent
 */
class BreadcrumbsPageComponent extends BaseBreadcrumbsComponent {
	/**
	 * The constructor.
	 */
	constructor() {
		super();

		const container = create(
			'div',
			[CSS.breadcrumbs],
			{},
			create('div', [CSS.layoutDashboardContent], {}, this)
		);

		create(
			'am-link',
			[CSS.breadcrumbsItem],
			{ [Attr.target]: Route.home },
			container,
			App.text('dashboardTitle')
		);

		this.init(container);
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		super.connectedCallback();
	}

	/**
	 * Fetch the breadcrumb data and init the componenten.
	 *
	 * @param container
	 * @async
	 */
	private async init(container: HTMLElement): Promise<void> {
		const url = getPageURL();
		const response = await requestAPI(PageController.breadcrumbs, { url });

		this.render(container, response.data);
	}

	/**
	 * Render the actual component.
	 *
	 * @param container
	 * @param data
	 */
	private render(container: HTMLElement, data: KeyValueMap): void {
		if (!data) {
			return;
		}

		data.forEach((page: KeyValueMap, index: number) => {
			const target = `${Route.page}?url=${encodeURIComponent(page.url)}`;

			const link = create(
				'am-link',
				[CSS.breadcrumbsItem],
				{ [Attr.target]: target },
				container
			);

			if (index == data.length - 1) {
				link.innerHTML = html`<span ${Attr.bind}="title">
					$${page.title}
				</span>`;
			} else {
				link.textContent = page.title;
			}
		});
	}
}

customElements.define('am-breadcrumbs-page', BreadcrumbsPageComponent);
