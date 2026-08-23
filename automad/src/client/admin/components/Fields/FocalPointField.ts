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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import {
	App,
	create,
	createGenericModal,
	CSS,
	FieldTag,
	fire,
	FormDataProviders,
	type UndoValue,
} from '@/admin/core';
import { BaseFieldComponent } from './BaseField';
import { FocalPointPickerComponent } from '../FocalPointPicker';
import type { FocalPoint } from '@/admin/types';

/**
 * A focal point field.
 *
 * @extends BaseFieldComponent
 */
export class FocalPointFieldComponent extends BaseFieldComponent {
	/**
	 * The field value.
	 */
	value: FocalPoint = null;

	/**
	 * The actual image as context. Note that in order to use the picker,
	 * the image setter has to be used.
	 */
	private _image: string;

	set image(image: string) {
		// If the component has an image and the image has changed,
		// reset the focal point. Especially testing for !!this._image
		// is essential here in order to prevent resets on component
		// initialization.
		if (!!this._image && this._image !== image) {
			this.value = null;

			fire('change', this);
		}

		this._image = image;
		this.button.disabled = !image;
	}

	/**
	 * The actual button that opens the modal.
	 */
	private button: HTMLButtonElement;

	/**
	 * Render the field.
	 */
	protected createInput(): void {
		const { name, value, label } = this._data;

		this.setAttribute('name', name);

		this.value = value as FocalPoint;

		this.button = create(
			'button',
			[CSS.button, CSS.buttonPrimary],
			{},
			this,
			label
		);

		const createFocalPointModal = () => {
			if (!this._image) {
				return;
			}

			const { modal, body } = createGenericModal(App.text('focalPoint'));

			const picker = create<FocalPointPickerComponent>(
				FocalPointPickerComponent.TAG_NAME,
				[],
				{},
				body
			);

			picker.init(this._image, this.value);

			modal.listen(picker, 'change', () => {
				this.value = picker.value;

				fire('change', this);
			});

			setTimeout(() => {
				modal.open();
			}, 0);
		};

		this.listen(this.button, 'click', createFocalPointModal);
	}

	/**
	 * This field has no label.
	 */
	protected createLabel(): void {}

	/**
	 * Return the field that is observed for changes.
	 *
	 * @return the input field
	 */
	getValueProvider(): HTMLElement {
		return this;
	}

	/**
	 * A function that can be used to mutate the field value.
	 *
	 * @param value
	 */
	async mutate(value: UndoValue): Promise<void> {
		this.value = value;
	}

	/**
	 * Query the current field value.
	 *
	 * @return the current value
	 */
	query() {
		return this.value;
	}
}

FormDataProviders.add(FieldTag.focalPoint);
customElements.define(FieldTag.focalPoint, FocalPointFieldComponent);
