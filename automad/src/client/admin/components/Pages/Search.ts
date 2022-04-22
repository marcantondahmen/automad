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
 * The search and replace view.
 *
 * @extends SidebarLayoutComponent
 */
export class SearchComponent extends SidebarLayoutComponent {
	/**
	 * Set the page title that is used a document title suffix.
	 */
	protected get pageTitle(): string {
		return App.text('searchTitle');
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
							target="${Routes.search}"
						>
							<am-icon-text
								icon="search"
								text="${App.text('searchTitle')}"
							></am-icon-text>
						</am-link>
					</div>
				</nav>
			</section>
			<section class="am-l-main__row">
				<div class="am-l-main__content">
					<am-search-form></am-search-form>
				</div>
			</section>
		`;
	}
}

customElements.define(getTagFromRoute(Routes.search), SearchComponent);
