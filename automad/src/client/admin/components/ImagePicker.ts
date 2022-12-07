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

import { Binding, Bindings, create, CSS, listen, requestAPI } from '../core';
import { Image } from '../types';
import { BaseComponent } from './Base';
import { ModalComponent } from './Modal/Modal';

/**
 * An image picker component.
 *
 * @example
 * <am-image-picker
 *     label="Shared Files"
 *     binding="bindingName"
 * ></am-image-picker>
 * <am-image-picker
 *      page="url"
 *      label="Page Files"
 *      binding="bindingName"
 * ></am-image-picker>
 *
 * @extends BaseComponent
 */
class ImagePickerComponent extends BaseComponent {
	/**
	 * The binding that refers to the actual picked value.
	 */
	private binding: Binding = null;

	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['page', 'label', 'binding'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.binding = Bindings.get(this.elementAttributes.binding);
		this.init();
	}

	/**
	 * Request the file list and initialze the picker.
	 *
	 * @async
	 */
	private async init(): Promise<void> {
		this.classList.add(CSS.field);

		if (this.elementAttributes.label) {
			create('label', [CSS.fieldLabel], {}, this).textContent =
				this.elementAttributes.label;
		}

		const wrapper = create('div', [], {}, this);
		wrapper.innerHTML = '<am-spinner></am-spinner>';

		const { data } = await requestAPI('ImageCollection/list', {
			url: this.elementAttributes.page || '',
		});

		wrapper.innerHTML = '';

		const { images } = data;

		if (!images) {
			return;
		}

		this.renderGrid(images, wrapper);
	}

	/**
	 * Render the image grid.
	 *
	 * @param images
	 */
	private renderGrid(images: Image[], wrapper: HTMLElement) {
		const grid = create('div', [CSS.imagePicker], {}, wrapper);
		const base = this.elementAttributes.page ? '' : '/shared/';
		const modal = (this.closest('am-modal') as ModalComponent) || null;

		images.forEach((image: Image) => {
			const img = create(
				'img',
				[CSS.imagePickerImage],
				{
					src: image.thumbnail,
					value: `${base}${image.name}`,
					'am-tooltip': image.name,
				},
				grid
			);

			listen(img, 'click', () => {
				this.binding.value = img.getAttribute('value');

				if (modal) {
					modal.close();
				}
			});
		});
	}
}

customElements.define('am-image-picker', ImagePickerComponent);
