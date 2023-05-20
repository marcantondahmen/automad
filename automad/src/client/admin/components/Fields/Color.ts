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

import { create, CSS, listen } from '@/core';
import { BaseFieldComponent } from './BaseField';

/**
 * A color field.
 *
 * @extends BaseFieldComponent
 */
class ColorComponent extends BaseFieldComponent {
	/**
	 * Create an input field.
	 */
	createInput(): void {
		const { name, id, value } = this._data;
		const combo = create('div', [CSS.inputCombo], {}, this);
		const input = create(
			'input',
			[CSS.input, CSS.textMono],
			{ id, name, value, type: 'text' },
			combo
		);
		const picker = create(
			'input',
			[],
			{ type: 'color', value },
			create('span', [CSS.inputComboColor], {}, combo)
		);

		listen(picker, 'change', () => {
			input.value = picker.value;
		});

		listen(input, 'keyup', () => {
			picker.value = input.value;
		});
	}
}

customElements.define('am-color', ColorComponent);
