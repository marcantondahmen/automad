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
 * Copyright (c) 2021-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { create, CSS, FieldTag, html } from '@/admin/core';
import { BaseFieldComponent } from './BaseField';

/**
 * A checkbox field.
 *
 * @extends BaseFieldComponent
 */
export class ToggleComponent extends BaseFieldComponent {
	/**
	 * Checkbox styles.
	 */
	protected classes = [CSS.toggle, CSS.toggleButton];

	/**
	 * Render the input field.
	 */
	createInput(): void {
		const { name, id, value, label } = this._data;

		create(
			'div',
			this.classes,
			{},
			this,
			html`
				<input
					type="checkbox"
					name="${name}"
					id="${id}"
					value="1"
					${value ? 'checked' : ''}
				/>
				<label for="${id}">
					<i class="bi"></i>
					<span>${label}</span>
				</label>
			`
		);
	}

	/**
	 * Query the current field value.
	 *
	 * @return the current value
	 */
	query() {
		return (this.input as HTMLInputElement).checked;
	}

	/**
	 * A function that can be used to mutate the field value.
	 *
	 * @param value
	 */
	mutate(value: any): void {
		(this.input as HTMLInputElement).checked = value;
	}
}

customElements.define(FieldTag.toggle, ToggleComponent);
