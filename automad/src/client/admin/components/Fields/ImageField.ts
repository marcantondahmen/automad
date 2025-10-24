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
	Binding,
	create,
	createImagePickerModal,
	CSS,
	FieldTag,
	fire,
	html,
	resizeImageUrl,
} from '@/admin/core';
import { ImgComponent } from '../Img';
import { BaseFieldComponent } from './BaseField';

/**
 * An image selection field.
 *
 * @extends BaseFieldComponent
 */
class ImageFieldComponent extends BaseFieldComponent {
	/**
	 * Create an input field.
	 */
	protected createInput(): void {
		const { name, id, value, placeholder } = this._data;
		const wrapper = create('span', [CSS.imageSelect], {}, this);

		const input = create('input', [CSS.input], {
			id,
			name,
			type: 'text',
			placeholder,
			value,
		});

		const preview = this.createPreview(wrapper, input, id);
		const combo = create('div', [CSS.imageSelectCombo], {}, wrapper);

		combo.appendChild(input);

		const button = this.createModalButton(combo);

		const createModal = (): void => {
			createImagePickerModal(
				(value) => {
					input.value = value;
					fire('change', input);
				},
				this._data.label,
				input.value
			);
		};

		this.listen(button, 'click', createModal.bind(this));
		this.listen(preview, 'click', createModal.bind(this));
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
		const previewBindingName = `imageSelectComponent_preview_${id}`;

		new Binding(previewBindingName, {
			input,
			modifier: (value: string): string => {
				return resizeImageUrl(value.split('?')[0]);
			},
		});

		return create(
			ImgComponent.TAG_NAME,
			[CSS.imageSelectPreview],
			{
				[Attr.bind]: previewBindingName,
				[Attr.bindTo]: 'src',
			},
			container
		);
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

customElements.define(FieldTag.image, ImageFieldComponent);
