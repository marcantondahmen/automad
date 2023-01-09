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
import { Section } from '../Switcher/Switcher';
import { BaseDashboardLayoutComponent } from './BaseDashboardLayout';

const renderMenu = (): string => {
	return html`
		<section
			class="${CSS.layoutDashboardSection} ${CSS.layoutDashboardSectionSticky}"
		>
			<div
				class="${CSS.layoutDashboardContent} ${CSS.layoutDashboardContentRow}"
			>
				<am-switcher class="${CSS.menu}">
					<am-switcher-link
						class="${CSS.menuItem}"
						${Attr.section}="${Section.settings}"
					>
						${App.text('fieldsSettings')}
					</am-switcher-link>
					<am-switcher-link
						class="${CSS.menuItem}"
						${Attr.section}="${Section.text}"
					>
						${App.text('fieldsContent')}
					</am-switcher-link>
					<am-switcher-link
						class="${CSS.menuItem}"
						${Attr.section}="${Section.colors}"
					>
						${App.text('fieldsColors')}
					</am-switcher-link>
					<am-switcher-link
						class="${CSS.menuItem}"
						${Attr.section}="${Section.files}"
					>
						${App.text('uploadedFiles')}
						<span class="${CSS.badge}">
							<am-file-count></am-file-count>
						</span>
					</am-switcher-link>
				</am-switcher>
				<am-filter placeholder="filterContent"></am-filter>
			</div>
		</section>
	`;
};

/**
 * The shared view.
 *
 * @extends BaseDashboardLayoutComponent
 */
export class SharedComponent extends BaseDashboardLayoutComponent {
	/**
	 * Set the page title that is used a document title suffix.
	 */
	protected get pageTitle(): string {
		return App.text('sharedTitle');
	}

	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderMainPartial(): string {
		return html`
			<am-breadcrumbs-route
				${Attr.target}="${Route.shared}"
				${Attr.text}="${this.pageTitle}"
			></am-breadcrumbs-route>
			${renderMenu()}
			<section class="${CSS.layoutDashboardSection}">
				<div class="${CSS.layoutDashboardContent}">
					<am-shared-data-form
						${Attr.api}="Shared/data"
					></am-shared-data-form>
					<am-switcher-section name="${Section.files}">
						<am-upload></am-upload>
						<div class="${CSS.flex} ${CSS.flexGap}">
							<am-modal-toggle
								class="${CSS.button} ${CSS.buttonAccent}"
								${Attr.modal}="#am-file-import-modal"
							>
								${App.text('importFromUrl')}
							</am-modal-toggle>
							<am-file-collection-submit
								class="${CSS.button} ${CSS.buttonPrimary}"
								${Attr.form}="FileCollection/list"
							>
								${App.text('deleteSelected')}
							</am-file-collection-submit>
						</div>
						<am-file-collection-list-form
							${Attr.confirm}="${App.text(
								'confirmDeleteSelectedFiles'
							)}"
							${Attr.api}="FileCollection/list"
						></am-file-collection-list-form>
					</am-switcher-section>
				</div>
			</section>
		`;
	}
}

customElements.define(getTagFromRoute(Route.shared), SharedComponent);
