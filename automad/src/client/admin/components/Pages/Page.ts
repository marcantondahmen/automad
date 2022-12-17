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
	Attr,
	CSS,
	EventName,
	getPageURL,
	getTagFromRoute,
	html,
	Route,
} from '../../core';
import { Section } from '../Switcher/Switcher';
import { BaseDashboardLayoutComponent } from './BaseDashboardLayout';

const renderBreadcrumbs = (): string => {
	return html`
		<section class="${CSS.layoutDashboardSection}">
			<div class="${CSS.layoutDashboardContent}">
				<am-breadcrumbs-page></am-breadcrumbs-page>
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
				<am-form ${Attr.api}="Page/move">
					<div class="${CSS.modalHeader}">
						<span>${App.text('movePage')}</span>
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
								${Attr.hideCurrent}
							></am-page-select-tree>
						</div>
					</div>
					<div class="${CSS.modalFooter}">
						<am-modal-close
							class="${CSS.button} ${CSS.buttonPrimary}"
						>
							${App.text('cancel')}
						</am-modal-close>
						<am-submit class="${CSS.button} ${CSS.buttonAccent}">
							${App.text('movePage')}
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
		<am-dropdown ${Attr.right}>
			<span class="${CSS.menuItem}">
				<span>${App.text('more')}</span>
				<span class="${CSS.dropdownArrow}"></span>
			</span>
			<div class="${CSS.dropdownItems}">
				<a
					${Attr.bind}="pageUrlWithBase"
					${Attr.bindTo}="href"
					class="${CSS.dropdownLink}"
					target="_blank"
				>
					<am-icon-text
						${Attr.icon}="pencil"
						${Attr.text}="${App.text('inPageEdit')}"
					></am-icon-text>
				</a>
				<am-form ${Attr.api}="Page/duplicate">
					<am-submit class="${CSS.dropdownLink}">
						<am-icon-text
							${Attr.icon}="files"
							${Attr.text}="${App.text('duplicatePage')}"
						></am-icon-text>
					</am-submit>
				</am-form>
				<am-modal-toggle
					class="${CSS.dropdownLink}"
					${Attr.modal}="#am-move-page-modal"
				>
					<am-icon-text
						${Attr.icon}="arrows-move"
						${Attr.text}="${App.text('movePage')}"
					></am-icon-text>
				</am-modal-toggle>
				<am-form
					${Attr.api}="Page/delete"
					${Attr.confirm}="${App.text('confirmDeletePage')}"
				>
					<am-submit class="${CSS.dropdownLink}">
						<am-icon-text
							${Attr.icon}="trash2"
							${Attr.text}="${App.text('deletePage')}"
						></am-icon-text>
					</am-submit>
				</am-form>
				<am-copy class="${CSS.dropdownLink}" value="${getPageURL()}">
					<am-icon-text
						${Attr.icon}="clipboard-plus"
						${Attr.text}="${App.text('copyUrlClipboard')}"
					></am-icon-text>
				</am-copy>
			</div>
		</am-dropdown>
	`;
};

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
						${App.text('pageSettings')}
					</am-switcher-link>
					<am-switcher-link
						class="${CSS.menuItem}"
						${Attr.section}="${Section.text}"
					>
						${App.text('pageContent')}
					</am-switcher-link>
					<am-switcher-link
						class="${CSS.menuItem}"
						${Attr.section}="${Section.colors}"
					>
						${App.text('pageColors')}
					</am-switcher-link>
					<am-switcher-link
						class="${CSS.menuItem}"
						${Attr.section}="${Section.files}"
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
					${Attr.api}="File/import"
					${Attr.event}="${EventName.filesChangeOnServer}"
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
						<am-modal-close
							class="${CSS.button} ${CSS.buttonPrimary}"
						>
							${App.text('cancel')}
						</am-modal-close>
						<am-submit class="${CSS.button} ${CSS.buttonAccent}">
							${App.text('importFromUrl')}
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
			<section class="${CSS.layoutDashboardSection}">
				<div class="${CSS.layoutDashboardContent}">
					<am-page-data-form
						${Attr.api}="Page/data"
					></am-page-data-form>
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
			${renderMovePageModal()}${renderFileImportModal()}
		`;
	}
}

customElements.define(getTagFromRoute(Route.page), PageComponent);
