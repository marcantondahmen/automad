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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, classes, create, html, listen, query } from '../../core';
import { FieldComponent } from './Field';

/**
 * A checkbox field that can have a default global value.
 *
 * @extends FieldComponent
 */
class CheckboxSelectComponent extends FieldComponent {
	/**
	 * Render the input field.
	 */
	createInput(): void {
		const { name, id, value, label, placeholder } = this._data;

		const wrapper = create(
			'div',
			[classes.checkbox, classes.checkboxSelect],
			{},
			this
		);

		const toggle = () => {
			wrapper.classList.toggle(classes.checkboxOff, select.value === '0');
			wrapper.classList.toggle(classes.checkboxOn, select.value === '1');
		};

		wrapper.classList.toggle(classes.checkboxDefaultOn, placeholder != '');

		wrapper.innerHTML = html`
			<label for="${id}"><span></span>${label}</label>
			<select name="${name}" id="${id}">
				<option value="">${App.text('useSharedDefault')}</option>
				<option value="0">${App.text('disable')}</option>
				<option value="1">${App.text('enable')}</option>
			</select>
		`;

		const select = query('select', wrapper) as HTMLSelectElement;
		select.value = (value as string) || '';

		this.listeners.push(listen(select, 'change', toggle.bind(this)));
		toggle();
	}
}

customElements.define('am-checkbox-select', CheckboxSelectComponent);
