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
					$${App.text('btn_more')}
					<div class="${classes.dropdownItems}">
						<a
							href="${App.baseURL}${getPageURL()}"
							class="${classes.dropdownItem}"
							target="_blank"
						>
							<i class="bi bi-pencil"></i>
							<span>$${App.text('btn_inpage_edit')}</span>
						</a>
						<am-form api="Page/duplicate">
							<am-submit class="${classes.dropdownItem}">
								<i class="bi bi-files"></i>
								<span>
									$${App.text('btn_duplicate_page')}
								</span>
							</am-submit>
						</am-form>
						<am-modal-toggle
							class="${classes.dropdownItem}"
							modal="#am-move-page-modal"
						>
							<i class="bi bi-arrows-move"></i>
							<span>$${App.text('btn_move_page')}</span>
						</am-modal-toggle>
						<am-form
							api="Page/delete"
							confirm="$${App.text('confirm_delete_page')}"
						>
							<am-submit class="${classes.dropdownItem}">
								<i class="bi bi-trash2"></i>
								<span>$${App.text('btn_delete_page')}</span>
							</am-submit>
						</am-form>
						<am-copy
							class="${classes.dropdownItem}"
							value="${getPageURL()}"
						>
							<i class="bi bi-clipboard-plus"></i>
							<span>
								$${App.text('btn_copy_url_clipboard')}
							</span>
						</am-copy>
					</div>
				</am-dropdown>
			`;

			movePageModal = html`
				<am-modal id="am-move-page-modal">
					<div class="${classes.modalDialog}">
						<am-form api="Page/move">
							<div class="${classes.modalHeader}">
								<span>$${App.text('btn_move_page')}</span>
								<am-modal-close
									class="${classes.modalClose}"
								></am-modal-close>
							</div>
							<div class="${classes.field}">
								<label class="${classes.fieldLabel}">
									${App.text('page_move_destination')}
								</label>
								<am-page-select-tree
									hidecurrent
								></am-page-select-tree>
							</div>
							<div class="${classes.modalFooter}">
								<am-submit
									class="${classes.button} ${classes.buttonSuccess}"
								>
									$${App.text('btn_move_page')}
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
								$${App.text('page_settings')}
							</am-switcher-link>
							<am-switcher-link
								section="${App.sections.content.text}"
							>
								$${App.text('page_vars_content')}
							</am-switcher-link>
							<am-switcher-link
								section="${App.sections.content.colors}"
							>
								$${App.text('page_vars_color')}
							</am-switcher-link>
							<am-switcher-link
								section="${App.sections.content.files}"
							>
								$${App.text('btn_files')}
								<am-file-count></am-file-count>
							</am-switcher-link>
						</am-switcher>
						${more}
					</div>
				</menu>
			</section>
			<section class="am-l-main__row">
				<div class="am-l-main__content">
					<am-page-data api="Page/data" watch></am-page-data>
					<am-switcher-section name="${App.sections.content.files}">
						<am-upload></am-upload>
						<am-modal-toggle
							class="${classes.button}"
							modal="#am-file-import-modal"
						>
							${App.text('btn_import')}
						</am-modal-toggle>
						<am-submit
							class="${classes.button}"
							form="FileCollection/list"
						>
							$${App.text('btn_remove_selected')}
						</am-submit>
						<am-file-collection-list
							confirm="$${App.text('confirm_delete_files')}"
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
							<span>$${App.text('btn_import')}</span>
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
								$${App.text('btn_import')}
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
		return html`<am-submit
			form="Page/data"
			title="$${App.text('btn_save')}"
		>
			<i class="bi bi-check"></i>
		</am-submit>`;
	}
}

customElements.define(getTagFromRoute(Routes[Routes.page]), PageComponent);
