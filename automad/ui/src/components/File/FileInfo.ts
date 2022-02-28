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
import { filesChangedOnServerEventName } from '../Forms/FileCollectionList';
import { ModalComponent } from '../Modal/Modal';

/**
 * A file edit modal toggle component.
 *
 * @extends BaseComponent
 */
export class FileInfoComponent extends BaseComponent {
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
		return html`
			<am-form
				api="File/editInfo"
				event="${filesChangedOnServerEventName}"
				class="${classes.modalDialog}"
			>
				<div class="${classes.modalHeader}">
					<span>${App.text('editFileInfo')}</span>
					<am-modal-close
						class="${classes.modalClose}"
					></am-modal-close>
				</div>
				<input type="hidden" name="old-name" value="${file.basename}" />
				${createField(
					'am-field',
					null,
					{
						key: 'new-name',
						value: file.basename,
						name: 'new-name',
						label: App.text('fileName'),
					},
					[]
				).outerHTML}
				${createField(
					'am-textarea',
					null,
					{
						key: 'caption',
						value: file.caption,
						name: 'caption',
						label: App.text('fileCaption'),
					},
					[]
				).outerHTML}
				<div class="${classes.modalFooter}">
					<am-modal-close class="${classes.button}">
						${App.text('close')}
					</am-modal-close>
					<a
						href="${file.url}"
						class="${classes.button}"
						download="${file.basename}"
					>
						${App.text('downloadFile')}
					</a>
					<am-submit class="${classes.button}">
						${App.text('save')}
					</am-submit>
				</div>
			</am-form>
		`;
	}
}

customElements.define('am-file-info', FileInfoComponent);
