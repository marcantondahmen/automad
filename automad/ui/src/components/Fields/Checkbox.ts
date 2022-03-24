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
 * Copyright (c) 2021-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { classes, create, html } from '../../core';
import { FieldComponent } from './Field';

/**
 * A checkbox field.
 *
 * @extends FieldComponent
 */
export class CheckboxComponent extends FieldComponent {
	/**
	 * Checkbox styles.
	 */
	protected classes = [classes.checkbox, classes.checkboxInput];

	/**
	 * Render the input field.
	 */
	renderInput(): void {
		const { name, id, value, label } = this._data;
		const wrapper = create('div', this.classes, {}, this);

		wrapper.innerHTML = html`
			<input
				type="checkbox"
				name="${name}"
				id="${id}"
				value="1"
				${value ? 'checked' : ''}
			/>
			<label for="${id}"><span></span>${label}</label>
		`;
	}
}

customElements.define('am-checkbox', CheckboxComponent);
