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
	getPageURL,
	getTagFromRoute,
	html,
	PageController,
	Route,
} from '@/admin/core';
import { HistoryModalFormComponent } from '@/admin/components/Forms/HistoryModalForm';
import { Section } from '@/common';
import { BaseDashboardLayoutComponent } from './BaseDashboardLayout';
import { renderFileImportModal } from './Partials/FileImportModal';

const renderBreadcrumbs = (): string => {
	return html`<am-breadcrumbs-page></am-breadcrumbs-page>`;
};

const renderHistoryModal = (): string => {
	return html`<am-history-modal-form></am-history-modal-form>`;
};

const renderMovePageModal = (): string => {
	if (getPageURL() === '/') {
		return '';
	}

	return html`
		<am-modal id="am-move-page-modal">
			<am-modal-dialog>
				<am-form ${Attr.api}="${PageController.move}">
					<am-modal-header>${App.text('movePage')}</am-modal-header>
					<am-modal-body>
						<div class="${CSS.field}">
							<label class="${CSS.fieldLabel}">
								${App.text('selectTargetMovePage')}
							</label>
							<am-page-select-tree
								${Attr.hideCurrent}
							></am-page-select-tree>
						</div>
					</am-modal-body>
					<am-modal-footer>
						<am-modal-close class="${CSS.button}">
							${App.text('cancel')}
						</am-modal-close>
						<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
							${App.text('movePage')}
						</am-submit>
					</am-modal-footer>
				</am-form>
			</am-modal-dialog>
		</am-modal>
	`;
};

const renderDropdown = (): string => {
	let subpageItems = '';

	if (getPageURL() != '/') {
		subpageItems = html`
			<am-form ${Attr.api}="${PageController.duplicate}">
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
			<am-copy
				class="${CSS.dropdownLink} ${CSS.dropdownDivider}"
				value="${getPageURL()}"
			>
				<am-icon-text
					${Attr.icon}="clipboard-plus"
					${Attr.text}="${App.text('copyUrlClipboard')}"
				></am-icon-text>
			</am-copy>
			<am-form
				${Attr.api}="${PageController.delete}"
				${Attr.confirm}="${App.text('confirmDeletePage')}"
			>
				<am-submit class="${CSS.dropdownLink}">
					<am-icon-text
						${Attr.icon}="trash3"
						${Attr.text}="${App.text('deletePage')}"
					></am-icon-text>
				</am-submit>
			</am-form>
		`;
	}

	return html`
		<am-dropdown ${Attr.right}>
			<span class="${CSS.menuItem}">
				<span class="${CSS.displaySmallNone}">${App.text('more')}</span>
				<span
					class="${CSS.displaySmallNone} ${CSS.dropdownArrow}"
				></span>
				<span
					class="${CSS.displaySmall} ${CSS.button} ${CSS.buttonIcon}"
				>
					<i class="bi bi-three-dots-vertical"></i>
				</span>
			</span>
			<div class="${CSS.dropdownItems}">
				<am-modal-toggle
					class="${CSS.dropdownLink}"
					${Attr.modal}="#${HistoryModalFormComponent.MODAL_ID}"
				>
					<am-icon-text
						${Attr.icon}="clock-history"
						${Attr.text}="${App.text('pageHistory')}"
					></am-icon-text>
				</am-modal-toggle>
				<span class="${CSS.dropdownDivider}"></span>
				<a
					href="${App.baseIndex}${getPageURL()}"
					class="${CSS.dropdownLink}"
				>
					<am-icon-text
						${Attr.icon}="pencil"
						${Attr.text}="${App.text('inPageEdit')}"
					></am-icon-text>
				</a>
				${subpageItems}
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
				<am-private-indicator
					class="${CSS.displaySmallNone}"
				></am-private-indicator>
				${renderDropdown()}
			</div>
		</section>
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
		if (App.system.i18n && getPageURL() === '/') {
			return html`
				${renderBreadcrumbs()}
				<section class="${CSS.layoutDashboardSection}">
					<div class="${CSS.layoutDashboardContent}">
						<am-alert
							${Attr.icon}="globe"
							${Attr.text}="i18nEnabled"
						></am-alert>
						<am-link
							${Attr.target}="${Route.system}?section=${Section.i18n}"
							class="${CSS.button}"
						>
							${App.text('systemI18n')}
						</am-link>
					</div>
				</section>
			`;
		}

		return html`
			${renderBreadcrumbs()}${renderMenu()}
			<section class="${CSS.layoutDashboardSection}">
				<div class="${CSS.layoutDashboardContent}">
					<am-page-data-form
						${Attr.api}="${PageController.data}"
					></am-page-data-form>
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
			${renderMovePageModal()}${renderFileImportModal()}
			${renderHistoryModal()}
		`;
	}

	/**
	 * Render an optional publish form.
	 *
	 * @returns the rendered HTML
	 */
	protected renderPublishForm(): string {
		return html`<am-page-publish-form></am-page-publish-form>`;
	}
}

customElements.define(getTagFromRoute(Route.page), PageComponent);
