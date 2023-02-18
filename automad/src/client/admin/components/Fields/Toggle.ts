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

import { create, CSS, html } from '../../core';
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
}

customElements.define('am-toggle', ToggleComponent);
