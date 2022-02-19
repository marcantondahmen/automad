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

import { App, classes, createField, html } from '../../core';
import { File } from '../../types';
import { FilesChangedOnServerEventName } from '../Forms/FileCollectionList';
import { BaseFileEditComponent } from './BaseFileEdit';

/**
 * A file edit modal toggle component.
 *
 * @extends BaseFileEditComponent
 */
export class FileEditComponent extends BaseFileEditComponent {
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
				event="${FilesChangedOnServerEventName}"
				class="${classes.modalDialog}"
			>
				<div class="${classes.modalHeader}">
					<span>${App.text('btn_edit_file_info')}</span>
					<am-modal-close
						class="${classes.modalClose}"
					></am-modal-close>
				</div>
				<img src="${file.url}" />
				<input type="hidden" name="old-name" value="${file.basename}" />
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
