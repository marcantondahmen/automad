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
export class ImageCopyResizedComponent extends BaseFileEditComponent {
	/**
	 * Render the actual modal markup.
	 *
	 * @param file
	 * @returns the modal markup
	 */
	protected renderModal(file: File): string {
		return html`
			<am-form
				api="Image/copyResized"
				event="${FilesChangedOnServerEventName}"
				class="${classes.modalDialog}"
			>
				<div class="${classes.modalHeader}">
					<span>${App.text('btn_copy_resized')}</span>
					<am-modal-close
						class="${classes.modalClose}"
					></am-modal-close>
				</div>
				<input
					class="${classes.input}"
					type="text"
					name="filename"
					value="${file.basename}"
					disabled
				/>
				${createField(
					'am-number',
					null,
					{
						key: 'width',
						value: file.width.toString(),
						name: 'width',
						label: App.text('image_width_px'),
					},
					[]
				).outerHTML}
				${createField(
					'am-number',
					null,
					{
						key: 'height',
						value: file.height.toString(),
						name: 'height',
						label: App.text('image_height_px'),
					},
					[]
				).outerHTML}
				${createField(
					'am-checkbox',
					null,
					{
						key: 'crop',
						value: 'on',
						name: 'crop',
						label: App.text('image_crop'),
					},
					[]
				).outerHTML}
				<div class="${classes.modalFooter}">
					<am-modal-close class="${classes.button}">
						${App.text('btn_close')}
					</am-modal-close>
					<am-submit class="${classes.button}">
						${App.text('btn_ok')}
					</am-submit>
				</div>
			</am-form>
		`;
	}
}

customElements.define('am-image-copy-resized', ImageCopyResizedComponent);
