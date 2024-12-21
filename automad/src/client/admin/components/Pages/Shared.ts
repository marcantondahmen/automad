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
 * Copyright (c) 2021-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	CSS,
	FileCollectionController,
	getTagFromRoute,
	html,
	Route,
	SharedController,
} from '@/admin/core';
import { Section } from '@/common';
import { BaseDashboardLayoutComponent } from './BaseDashboardLayout';
import { renderFileImportModal } from './Partials/FileImportModal';

const renderMenu = (): string => {
	return html`
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
							${Attr.section}="${Section.customizations}"
						>
							${App.text('fieldsCustomizations')}
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
					</div>
					<am-dropdown class="${CSS.displaySmall} ${CSS.button}">
						<span class="${CSS.iconText}">
							<am-switcher-label></am-switcher-label>
							<span class="${CSS.dropdownArrow}"></span>
						</span>
						<div class="${CSS.dropdownItems}">
							<am-switcher-link
								class="${CSS.dropdownLink}"
								${Attr.section}="${Section.settings}"
							>
								${App.text('fieldsSettings')}
							</am-switcher-link>
							<am-switcher-link
								class="${CSS.dropdownLink}"
								${Attr.section}="${Section.text}"
							>
								${App.text('fieldsContent')}
							</am-switcher-link>
							<am-switcher-link
								class="${CSS.dropdownLink}"
								${Attr.section}="${Section.customizations}"
							>
								${App.text('fieldsCustomizations')}
							</am-switcher-link>
							<am-switcher-link
								class="${CSS.dropdownLink}"
								${Attr.section}="${Section.files}"
							>
								${App.text('uploadedFiles')}
							</am-switcher-link>
						</div>
					</am-dropdown>
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
						${Attr.api}="${SharedController.data}"
					></am-shared-data-form>
					<am-switcher-section name="${Section.files}">
						<am-upload></am-upload>
						<div class="${CSS.flex} ${CSS.flexGap}">
							<am-modal-toggle
								class="${CSS.button} ${CSS.buttonPrimary}"
								${Attr.modal}="#am-file-import-modal"
							>
								${App.text('importFromUrl')}
							</am-modal-toggle>
							<am-file-collection-submit
								class="${CSS.button} ${CSS.buttonDanger}"
								${Attr.form}="${FileCollectionController.list}"
							>
								${App.text('deleteSelected')}
							</am-file-collection-submit>
						</div>
						<am-file-collection-list-form
							${Attr.confirm}="${App.text(
								'confirmDeleteSelectedFiles'
							)}"
							${Attr.api}="${FileCollectionController.list}"
						></am-file-collection-list-form>
					</am-switcher-section>
				</div>
			</section>
			${renderFileImportModal()}
		`;
	}

	/**
	 * Render an optional publish form.
	 *
	 * @returns the rendered HTML
	 */
	protected renderPublishForm(): string {
		return html`<am-shared-publish-form></am-shared-publish-form>`;
	}
}

customElements.define(getTagFromRoute(Route.shared), SharedComponent);
