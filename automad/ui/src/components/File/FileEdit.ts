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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, classes, create, createField, html, listen } from '../../core';
import { File } from '../../types';
import { BaseComponent } from '../Base';
import { FilesChangedOnServerEventName } from '../Forms/FileCollectionList';
import { ModalComponent } from '../Modal/Modal';

/**
 * A file edit modal toggle component.
 *
 * @extends BaseComponent
 */
export class FileEditComponent extends BaseComponent {
	/**
	 * The file data.
	 */
	data: File;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		listen(this, 'click', () => {
			if (!this.data) {
				console.log('File data is not defined');

				return;
			}

			const modal = this.createModal(this.data);

			setTimeout(() => {
				modal.open();
			}, 0);
		});
	}

	/**
	 * Create a file edit modal component.
	 *
	 * @param file
	 * @returns the created modal component
	 */
	protected createModal(file: File): ModalComponent {
		const modal = create('am-modal', [], { destroy: '' }, document.body);

		modal.innerHTML = this.renderModal(file);

		return modal;
	}

	/**
	 * Render the actual modal markup.
	 *
	 * @param file
	 * @returns the modal markup
	 */
	protected renderModal(file: File): string {
		let image = '';

		if (file.thumbnail) {
			image = html`
				<am-file-robot file="${file.url}">
					<img src="${file.url}" />
				</am-file-robot>
				<am-file-robot file="${file.url}" class="${classes.button}">
					${App.text('image_edit')}
				</am-file-robot>
			`;
		}

		return html`
			<am-form
				api="File/editInfo"
				event="${FilesChangedOnServerEventName}"
				class="${classes.modalDialog} ${classes.modalDialogFullscreen}"
			>
				<div class="${classes.modalHeader}">
					<span>${App.text('btn_edit_file_info')}</span>
					<am-modal-close
						class="${classes.modalClose}"
					></am-modal-close>
				</div>
				<div class="${classes.flex}">
					<div>
						${image}
						<input
							type="hidden"
							name="old-name"
							value="${file.basename}"
						/>
					</div>
					<span>
						${createField(
							'am-field',
							null,
							{
								key: 'new-name',
								value: file.basename,
								name: 'new-name',
								label: App.text('file_name'),
							},
							[]
						).outerHTML}
						${createField(
							'am-textarea',
							null,
							{
								key: 'caption',
								value: file.basename,
								name: 'caption',
								label: App.text('file_caption'),
							},
							[]
						).outerHTML}
					</span>
				</div>
				<div class="${classes.modalFooter}">
					<am-modal-close class="${classes.button}">
						${App.text('btn_close')}
					</am-modal-close>
					<a
						href="${file.url}"
						class="${classes.button}"
						download="${file.basename}"
					>
						${App.text('btn_download_file')}
					</a>
					<am-submit class="${classes.button}">
						${App.text('btn_save')}
					</am-submit>
				</div>
			</am-form>
		`;
	}
}

customElements.define('am-file-edit', FileEditComponent);
