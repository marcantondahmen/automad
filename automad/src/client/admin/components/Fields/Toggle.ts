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
	protected classes = [classes.toggle, classes.toggleInput];

	/**
	 * Render the input field.
	 */
	createInput(): void {
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

customElements.define('am-toggle', ToggleComponent);
