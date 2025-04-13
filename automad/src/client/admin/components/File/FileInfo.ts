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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	create,
	createField,
	CSS,
	EventName,
	FieldTag,
	FileController,
	html,
	listen,
} from '@/admin/core';
import { File } from '@/admin/types';
import { BaseComponent } from '@/admin/components/Base';
import { ModalComponent } from '@/admin/components/Modal/Modal';

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
		const modal = create(
			'am-modal',
			[],
			{ [Attr.destroy]: '' },
			document.body
		);

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
		let caption = '';

		if (App.fileTypesImage.includes(file.extension)) {
			caption = createField(
				FieldTag.textarea,
				null,
				{
					key: 'caption',
					value: file.caption,
					name: 'caption',
					label: App.text('imageCaption'),
				},
				[]
			).outerHTML;
		}

		return html`
			<am-form
				${Attr.api}="${FileController.editInfo}"
				${Attr.event}="${EventName.filesChangeOnServer}"
				class="${CSS.modalDialog}"
			>
				<am-modal-header>${App.text('editFileInfo')}</am-modal-header>
				<input type="hidden" name="old-name" value="${file.basename}" />
				<am-modal-body>
					${createField(
						FieldTag.input,
						null,
						{
							key: 'new-name',
							value: file.basename,
							name: 'new-name',
							label: App.text('fileName'),
						},
						[]
					).outerHTML}
					${caption}
				</am-modal-body>
				<am-modal-footer>
					<a
						href="${App.baseURL}${file.url}"
						class="${CSS.button}"
						target="_blank"
					>
						${App.text('downloadFile')}
					</a>
					<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
						${App.text('save')}
					</am-submit>
				</am-modal-footer>
			</am-form>
		`;
	}
}

customElements.define('am-file-info', FileInfoComponent);
