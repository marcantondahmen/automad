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

import {
	App,
	CSS,
	eventNames,
	getPageURL,
	getTagFromRoute,
	html,
	Routes,
} from '../../core';
import { Sections } from '../Switcher/Switcher';
import { BaseDashboardLayoutComponent } from './BaseDashboardLayout';

const renderBreadcrumbs = (): string => {
	return html`
		<section class="am-l-dashboard__section">
			<div class="am-l-dashboard__content">
				<am-page-breadcrumbs></am-page-breadcrumbs>
			</div>
		</section>
	`;
};

const renderMovePageModal = (): string => {
	if (getPageURL() === '/') {
		return '';
	}

	return html`
		<am-modal id="am-move-page-modal">
			<div class="${CSS.modalDialog}">
				<am-form api="Page/move">
					<div class="${CSS.modalHeader}">
						<span>$${App.text('movePage')}</span>
						<am-modal-close
							class="${CSS.modalClose}"
						></am-modal-close>
					</div>
					<div class="${CSS.modalBody}">
						<div class="${CSS.field}">
							<label class="${CSS.fieldLabel}">
								${App.text('selectTargetMovePage')}
							</label>
							<am-page-select-tree
								hidecurrent
							></am-page-select-tree>
						</div>
					</div>
					<div class="${CSS.modalFooter}">
						<am-modal-close class="${CSS.button}">
							$${App.text('cancel')}
						</am-modal-close>
						<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
							$${App.text('movePage')}
						</am-submit>
					</div>
				</am-form>
			</div>
		</am-modal>
	`;
};

const renderDropdown = (): string => {
	if (getPageURL() === '/') {
		return '';
	}

	return html`
		<am-dropdown right>
			<span class="${CSS.menuItem}">
				<span>${App.text('more')}</span>
				<i class="bi bi-chevron-down"></i>
			</span>
			<div class="${CSS.dropdownItems}">
				<a
					bind="pageUrlWithBase"
					bindto="href"
					class="${CSS.dropdownLink}"
					target="_blank"
				>
					<am-icon-text
						icon="pencil"
						text="${App.text('inPageEdit')}"
					></am-icon-text>
				</a>
				<am-form api="Page/duplicate">
					<am-submit class="${CSS.dropdownLink}">
						<am-icon-text
							icon="files"
							text="${App.text('duplicatePage')}"
						></am-icon-text>
					</am-submit>
				</am-form>
				<am-modal-toggle
					class="${CSS.dropdownLink}"
					modal="#am-move-page-modal"
				>
					<am-icon-text
						icon="arrows-move"
						text="${App.text('movePage')}"
					></am-icon-text>
				</am-modal-toggle>
				<am-form
					api="Page/delete"
					confirm="$${App.text('confirmDeletePage')}"
				>
					<am-submit class="${CSS.dropdownLink}">
						<am-icon-text
							icon="trash2"
							text="${App.text('deletePage')}"
						></am-icon-text>
					</am-submit>
				</am-form>
				<am-copy class="${CSS.dropdownLink}" value="${getPageURL()}">
					<am-icon-text
						icon="clipboard-plus"
						text="${App.text('copyUrlClipboard')}"
					></am-icon-text>
				</am-copy>
			</div>
		</am-dropdown>
	`;
};

const renderMenu = (): string => {
	return html`
		<section
			class="am-l-dashboard__section am-l-dashboard__section--sticky"
		>
			<div class="am-l-dashboard__content am-l-dashboard__content--row">
				<am-switcher class="${CSS.menu}">
					<am-switcher-link
						class="${CSS.menuItem}"
						section="${Sections.settings}"
					>
						${App.text('pageSettings')}
					</am-switcher-link>
					<am-switcher-link
						class="${CSS.menuItem}"
						section="${Sections.text}"
					>
						${App.text('pageContent')}
					</am-switcher-link>
					<am-switcher-link
						class="${CSS.menuItem}"
						section="${Sections.colors}"
					>
						${App.text('pageColors')}
					</am-switcher-link>
					<am-switcher-link
						class="${CSS.menuItem}"
						section="${Sections.files}"
					>
						${App.text('uploadedFiles')}
						<span class="am-e-badge">
							<am-file-count></am-file-count>
						</span>
					</am-switcher-link>
				</am-switcher>
				<am-filter placeholder="filterContent"></am-filter>
				<am-private-indicator></am-private-indicator>
				${renderDropdown()}
			</div>
		</section>
	`;
};

const renderFileImportModal = (): string => {
	return html`
		<am-modal id="am-file-import-modal">
			<div class="${CSS.modalDialog}">
				<am-form
					api="File/import"
					event="${eventNames.filesChangeOnServer}"
				>
					<div class="${CSS.modalBody}">
						<div class="${CSS.field}">
							<input
								class="${CSS.input}"
								name="importUrl"
								type="text"
								placeholder="URL"
							/>
						</div>
					</div>
					<div class="${CSS.modalFooter}">
						<am-modal-close class="${CSS.button} ${CSS.buttonLink}">
							$${App.text('cancel')}
						</am-modal-close>
						<am-submit class="${CSS.button} ${CSS.buttonAccent}">
							$${App.text('importFromUrl')}
						</am-submit>
					</div>
				</am-form>
			</div>
		</am-modal>
	`;
};

/**
 * The page view.
 *
 * @extends BaseDashboardLayoutComponent
 */
export class PageComponent extends BaseDashboardLayoutComponent {
	/**
	 * Set the page title that is used a document title suffix.
	 */
	protected get pageTitle(): string {
		return getPageURL();
	}

	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderMainPartial(): string {
		return html`
			${renderBreadcrumbs()}${renderMenu()}
			<section class="am-l-dashboard__section">
				<div class="am-l-dashboard__content">
					<am-page-data-form api="Page/data"></am-page-data-form>
					<am-switcher-section name="${Sections.files}">
						<am-upload></am-upload>
						<div class="${CSS.flex} ${CSS.flexGap}">
							<am-modal-toggle
								class="${CSS.button} ${CSS.buttonAccent}"
								modal="#am-file-import-modal"
							>
								${App.text('importFromUrl')}
							</am-modal-toggle>
							<am-file-collection-submit
								class="${CSS.button}"
								form="FileCollection/list"
							>
								$${App.text('deleteSelected')}
							</am-file-collection-submit>
						</div>
						<am-file-collection-list-form
							confirm="$${App.text('confirmDeleteSelectedFiles')}"
							api="FileCollection/list"
						></am-file-collection-list-form>
					</am-switcher-section>
				</div>
			</section>
			${renderMovePageModal()}${renderFileImportModal()}
		`;
	}
}

customElements.define(getTagFromRoute(Routes.page), PageComponent);
