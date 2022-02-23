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

import {
	App,
	classes,
	getPageURL,
	getTagFromRoute,
	html,
	Routes,
} from '../../core';
import { FilesChangedOnServerEventName } from '../Forms/FileCollectionList';
import { SidebarLayoutComponent } from './SidebarLayout';

/**
 * The page view.
 *
 * @extends SidebarLayoutComponent
 */
export class PageComponent extends SidebarLayoutComponent {
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
		let more = '';
		let movePageModal = '';

		if (getPageURL() !== '/') {
			more = html`
				<am-dropdown>
					$${App.text('more')}
					<div class="${classes.dropdownItems}">
						<a
							href="${App.baseURL}${getPageURL()}"
							class="${classes.dropdownItem}"
							target="_blank"
						>
							<am-icon-text
								icon="pencil"
								text="${App.text('inPageEdit')}"
							></am-icon-text>
						</a>
						<am-form api="Page/duplicate">
							<am-submit class="${classes.dropdownItem}">
								<am-icon-text
									icon="files"
									text="${App.text('duplicatePage')}"
								></am-icon-text>
							</am-submit>
						</am-form>
						<am-modal-toggle
							class="${classes.dropdownItem}"
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
							<am-submit class="${classes.dropdownItem}">
								<am-icon-text
									icon="trash2"
									text="${App.text('deletePage')}"
								></am-icon-text>
							</am-submit>
						</am-form>
						<am-copy
							class="${classes.dropdownItem}"
							value="${getPageURL()}"
						>
							<am-icon-text
								icon="clipboard-plus"
								text="${App.text('copyUrlClipboard')}"
							></am-icon-text>
						</am-copy>
					</div>
				</am-dropdown>
			`;

			movePageModal = html`
				<am-modal id="am-move-page-modal">
					<div class="${classes.modalDialog}">
						<am-form api="Page/move">
							<div class="${classes.modalHeader}">
								<span>$${App.text('movePage')}</span>
								<am-modal-close
									class="${classes.modalClose}"
								></am-modal-close>
							</div>
							<div class="${classes.field}">
								<label class="${classes.fieldLabel}">
									${App.text('selectTargetMovePage')}
								</label>
								<am-page-select-tree
									hidecurrent
								></am-page-select-tree>
							</div>
							<div class="${classes.modalFooter}">
								<am-submit
									class="${classes.button} ${classes.buttonSuccess}"
								>
									$${App.text('movePage')}
									<i class="bi bi-arrows-move"></i>
								</am-submit>
							</div>
						</am-form>
					</div>
				</am-modal>
			`;
		}

		return html`
			<section class="am-l-main__row">
				<nav class="am-l-main__content">
					<am-page-breadcrumbs></am-page-breadcrumbs>
				</nav>
			</section>
			<section class="am-l-main__row am-l-main__row--sticky">
				<menu class="am-l-main__content">
					<div class="am-u-flex">
						<am-switcher class="am-u-flex__item-grow">
							<am-switcher-link
								section="${App.sections.content.settings}"
							>
								$${App.text('pageSettings')}
							</am-switcher-link>
							<am-switcher-link
								section="${App.sections.content.text}"
							>
								$${App.text('pageContent')}
							</am-switcher-link>
							<am-switcher-link
								section="${App.sections.content.colors}"
							>
								$${App.text('pageColors')}
							</am-switcher-link>
							<am-switcher-link
								section="${App.sections.content.files}"
							>
								$${App.text('uploadedFiles')}
								<am-file-count></am-file-count>
							</am-switcher-link>
						</am-switcher>
						<am-private-indicator></am-private-indicator>
						${more}
					</div>
				</menu>
			</section>
			<section class="am-l-main__row">
				<div class="am-l-main__content">
					<am-page-data api="Page/data"></am-page-data>
					<am-switcher-section name="${App.sections.content.files}">
						<am-upload></am-upload>
						<am-modal-toggle
							class="${classes.button}"
							modal="#am-file-import-modal"
						>
							${App.text('importFromUrl')}
						</am-modal-toggle>
						<am-file-collection-submit>
							$${App.text('deleteSelected')}
						</am-file-collection-submit>
						<am-file-collection-list
							confirm="$${App.text('confirmDeleteSelectedFiles')}"
							api="FileCollection/list"
							watch
						></am-file-collection-list>
					</am-switcher-section>
				</div>
			</section>
			${movePageModal}
			<am-modal id="am-file-import-modal">
				<div class="${classes.modalDialog}">
					<am-form
						api="File/import"
						event="${FilesChangedOnServerEventName}"
					>
						<div class="${classes.modalHeader}">
							<span>$${App.text('importFromUrl')}</span>
							<am-modal-close
								class="${classes.modalClose}"
							></am-modal-close>
						</div>
						<div class="${classes.field}">
							<input
								class="${classes.input}"
								name="importUrl"
								type="text"
								placeholder="URL"
							/>
						</div>
						<div class="${classes.modalFooter}">
							<am-submit
								class="${classes.button} ${classes.buttonSuccess}"
							>
								$${App.text('importFromUrl')}
								<i class="bi bi-cloud-download"></i>
							</am-submit>
						</div>
					</am-form>
				</div>
			</am-modal>
		`;
	}

	/**
	 * Render the save button partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderSaveButtonPartial(): string {
		return html`<am-submit form="Page/data" title="$${App.text('save')}">
			<i class="bi bi-check"></i>
		</am-submit>`;
	}
}

customElements.define(getTagFromRoute(Routes[Routes.page]), PageComponent);
