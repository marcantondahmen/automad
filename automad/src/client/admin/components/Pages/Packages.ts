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
	App,
	Attr,
	create,
	CSS,
	getTagFromRoute,
	html,
	Route,
	Section,
} from '@/admin/core';
import { BaseDashboardLayoutComponent } from './BaseDashboardLayout';

/**
 * The packages view.
 *
 * @extends BaseDashboardLayoutComponent
 */
export class PackagesComponent extends BaseDashboardLayoutComponent {
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
		const menu = create('am-switcher-dropdown');

		menu.data = {
			overview: { icon: 'grid', section: Section.overview },
			items: [
				{
					title: App.text('packagesSwitcherTitle'),
					section: Section.packages,
				},
				{
					title: App.text('repositoriesSwitcherTitle'),
					section: Section.repositories,
				},
			],
		};

		return html`
			<am-breadcrumbs-route
				${Attr.target}="${Route.packages}"
				${Attr.text}="${this.pageTitle}"
			></am-breadcrumbs-route>
			${menu.outerHTML}
			<section class="${CSS.layoutDashboardSection}">
				<div class="${CSS.layoutDashboardContent}">
					<am-switcher-section name="${Section.overview}">
						<p>${App.text('packagesOverview')}</p>
						<div
							class="${CSS.grid} ${CSS.gridAuto}"
							style="--min: 24rem;"
						>
							<am-switcher-link
								class="${CSS.card} ${CSS.cardHover}"
								${Attr.section}="${Section.packages}"
							>
								<span class="${CSS.cardIcon}">
									<i class="bi bi-box-seam"></i>
								</span>
								<span class="${CSS.cardTitle}">
									${App.text('packagesSwitcherTitle')}
								</span>
								<span class="${CSS.cardBody}">
									<span class="${CSS.textLimitRows}">
										${App.text('packagesInfo')}
									</span>
								</span>
							</am-switcher-link>
							<am-switcher-link
								class="${CSS.card} ${CSS.cardHover}"
								${Attr.section}="${Section.repositories}"
							>
								<span class="${CSS.cardIcon}">
									<i class="bi bi-git"></i>
								</span>
								<span class="${CSS.cardTitle}">
									${App.text('repositoriesSwitcherTitle')}
								</span>
								<span class="${CSS.cardBody}">
									<span class="${CSS.textLimitRows}">
										${App.text('repositoriesInfo')}
									</span>
								</span>
							</am-switcher-link>
						</div>
					</am-switcher-section>
					<am-switcher-section name="${Section.packages}">
						<p>${App.text('packagesInfo')}</p>
						<am-package-list></am-package-list>
					</am-switcher-section>
					<am-switcher-section name="${Section.repositories}">
						<am-repository-list></am-repository-list>
					</am-switcher-section>
				</div>
			</section>
		`;
	}
}

customElements.define(getTagFromRoute(Route.packages), PackagesComponent);
