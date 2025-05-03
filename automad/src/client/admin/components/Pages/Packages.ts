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
		return html`
			<am-breadcrumbs-route
				${Attr.target}="${Route.packages}"
				${Attr.text}="${this.pageTitle}"
			></am-breadcrumbs-route>
			<section
				class="${CSS.layoutDashboardSection} ${CSS.layoutDashboardSectionSticky}"
			>
				<div
					class="${CSS.layoutDashboardContent} ${CSS.layoutDashboardContentRow}"
				>
					<am-switcher>
						<div class="${CSS.menu} ${CSS.displaySmallNone}">
							<am-switcher-link
								class="${CSS.menuItem}"
								${Attr.section}="${Section.packages}"
							>
								${App.text('packagesTitle')}
							</am-switcher-link>
							<am-switcher-link
								class="${CSS.menuItem}"
								${Attr.section}="${Section.repositories}"
							>
								${App.text('repositoriesTitle')}
							</am-switcher-link>
						</div>
						<am-dropdown class="${CSS.displaySmall} ${CSS.button}">
							<span class="${CSS.iconText}">
								<am-switcher-label></am-switcher-label>
								<span class="${CSS.dropdownArrow}"></span>
							</span>
							<div class="${CSS.dropdownItems}">
								<am-switcher-link
									class="${CSS.dropdownLink}"
									${Attr.section}="${Section.packages}"
								>
									${App.text('packagesTitle')}
								</am-switcher-link>
								<am-switcher-link
									class="${CSS.dropdownLink}"
									${Attr.section}="${Section.repositories}"
								>
									${App.text('repositoriesTitle')}
								</am-switcher-link>
							</div>
						</am-dropdown>
					</am-switcher>
					<am-filter placeholder="packagesFilter"></am-filter>
					<am-update-all-packages></am-update-all-packages>
				</div>
			</section>
			<section class="${CSS.layoutDashboardSection}">
				<div class="${CSS.layoutDashboardContent}">
					<am-switcher-section name="${Section.packages}">
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
