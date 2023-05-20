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
 * Copyright (c) 2021-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	Binding,
	create,
	createImagePickerModal,
	CSS,
	html,
	listen,
	resolveFileUrl,
} from '@/core';
import { BaseFieldComponent } from './BaseField';

/**
 * An image selection field.
 *
 * @extends BaseFieldComponent
 */
class ImageSelectComponent extends BaseFieldComponent {
	/**
	 * Create an input field.
	 */
	protected createInput(): void {
		const { name, id, value, placeholder } = this._data;
		const wrapper = create('span', [CSS.imageSelect], {}, this);
		const preview = create('span', [CSS.imageSelectPreview], {}, wrapper);
		const combo = create('div', [CSS.imageSelectCombo], {}, wrapper);

		const input = create(
			'input',
			[CSS.input],
			{
				id,
				name,
				type: 'text',
				placeholder,
				value,
			},
			combo
		);

		this.createPreview(preview, input, id);
		const button = this.createModalButton(combo);

		const inputBindingName = `input_${id}`;
		new Binding(inputBindingName, { input });

		const createModal = (): void => {
			createImagePickerModal(inputBindingName, this._data.label);
		};

		listen(button, 'click', createModal.bind(this));
		listen(preview, 'click', createModal.bind(this));
	}

	/**
	 * Create the preview element.
	 *
	 * @param container
	 * @param input
	 * @param id
	 */
	private createPreview(
		container: HTMLElement,
		input: HTMLInputElement,
		id: string
	) {
		const previewBindingName = `preview_${id}`;

		new Binding(previewBindingName, {
			input,
			modifier: (value: string): string => {
				container.classList.remove(CSS.imageSelectPreviewError);

				return resolveFileUrl(value).split('?')[0];
			},
		});

		const img = create(
			'img',
			[],
			{ [Attr.bind]: previewBindingName, [Attr.bindTo]: 'src' },
			container
		);

		listen(img, 'error', () => {
			img.removeAttribute('src');
			container.classList.add(CSS.imageSelectPreviewError);
		});
	}

	/**
	 * Create the modal button.
	 *
	 * @param container
	 * @returns the modal button
	 */
	private createModalButton(container: HTMLElement): HTMLElement {
		const button = create(
			'button',
			[CSS.button],
			{},
			container,
			html`
				<i class="bi bi-folder"></i>
				<span>${App.text('browseFiles')}</span>
			`
		);

		return button;
	}
}

customElements.define('am-image-select', ImageSelectComponent);
