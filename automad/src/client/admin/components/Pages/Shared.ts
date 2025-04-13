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
							<am-dropdown class="${CSS.displaySmall}">
								<span class="${CSS.button} ${CSS.buttonIcon}">
									<i class="bi bi-three-dots"></i>
								</span>
								<div class="${CSS.dropdownItems}">
									<am-file-collection-move
										class="${CSS.dropdownLink}"
										${Attr.form}="${FileCollectionController.list}"
									>
										<am-icon-text
											${Attr.icon}="folder-symlink"
											${Attr.text}="${App.text(
												'moveSelection'
											)}"
										></am-icon-text>
									</am-file-collection-move>
									<am-file-collection-delete
										class="${CSS.dropdownLink}"
										${Attr.form}="${FileCollectionController.list}"
									>
										<am-icon-text
											${Attr.icon}="trash3"
											${Attr.text}="${App.text(
												'deleteSelection'
											)}"
										></am-icon-text>
									</am-file-collection-delete>
								</div>
							</am-dropdown>
							<am-file-collection-move
								class="${CSS.button} ${CSS.displaySmallNone}"
								${Attr.form}="${FileCollectionController.list}"
							>
								${App.text('moveSelection')}
							</am-file-collection-move>
							<am-file-collection-delete
								class="${CSS.button} ${CSS.buttonDanger} ${CSS.displaySmallNone}"
								${Attr.form}="${FileCollectionController.list}"
							>
								${App.text('deleteSelection')}
							</am-file-collection-delete>
						</div>
						<am-file-collection-list-form
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
