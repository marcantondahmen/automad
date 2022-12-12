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

import { App, Attr, CSS, getTagFromRoute, html, Route } from '../../core';
import { BaseDashboardLayoutComponent } from './BaseDashboardLayout';

/**
 * The search and replace view.
 *
 * @extends BaseDashboardLayoutComponent
 */
export class SearchComponent extends BaseDashboardLayoutComponent {
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
			<section class="am-l-dashboard__section">
				<nav class="am-l-dashboard__content">
					<div class="${CSS.breadcrumbs}">
						<am-link
							class="${CSS.breadcrumbsItem}"
							${Attr.target}="${Route.search}"
						>
							<am-icon-text
								${Attr.icon}="search"
								${Attr.text}="${App.text('searchTitle')}"
							></am-icon-text>
						</am-link>
					</div>
					<am-search-form></am-search-form>
				</div>
			</section>
		`;
	}
}

customElements.define(getTagFromRoute(Route.search), SearchComponent);
