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

import { App, classes, getTagFromRoute, html, Routes } from '../../core';
import { SidebarLayoutComponent } from './SidebarLayout';

/**
 * The page view.
 *
 * @extends SidebarLayoutComponent
 */
export class PackagesComponent extends SidebarLayoutComponent {
	/**
	 * Set the page title that is used a document title suffix.
	 */
	protected get pageTitle(): string {
		return App.text('packagesTitle');
	}

	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderMainPartial(): string {
		return html`
			<section class="am-l-main__row">
				<nav class="am-l-main__content">
					<div class="${classes.breadcrumbs}">
						<am-link
							class="${classes.breadcrumbsItem}"
							target="system"
						>
							<am-icon-text
								icon="sliders"
								text="$${App.text('systemTitle')}"
							></am-icon-text>
						</am-link>
					</div>
				</nav>
			</section>
			<am-system-menu class="am-l-main__row am-l-main__row--sticky">
				<menu class="am-l-main__content">
					<am-filter placeholder="packagesFilter"></am-filter>
				</menu>
			</am-system-menu>
			<section class="am-l-main__row">
				<div class="am-l-main__content">
					<am-package-list></am-package-list>
				</div>
			</section>
		`;
	}
}

customElements.define(getTagFromRoute(Routes.packages), PackagesComponent);
