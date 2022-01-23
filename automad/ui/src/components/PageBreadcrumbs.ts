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

import { classes, getPageURL } from '../core/utils';
import { create } from '../core/create';
import { requestController } from '../core/request';
import { KeyValueMap } from '../core/types';
import { BaseComponent } from './Base';
import { App } from '../core/app';

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
	 */
	private async init(): Promise<void> {
		this.classList.add(classes.breadcrumbs);

		const response = await requestController(
			'PageController::breadcrumbs',
			{ url: getPageURL() }
		);

		this.render(response.data);
	}

	/**
	 * Render the actual component.
	 *
	 * @param data
	 */
	private render(data: KeyValueMap): void {
		data.forEach((page: KeyValueMap) => {
			const href = `${App.dashboardURL}/Page?url=${encodeURIComponent(
				page.url
			)}`;

			const link = create('a', [classes.breadcrumbsItem], { href }, this);

			link.innerHTML = `<i class="bi bi-chevron-right"></i> ${page.title}`;
		});
	}
}

customElements.define('am-page-breadcrumbs', PageBreadcrumbsComponent);
