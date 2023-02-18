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

import { CSS, create, listen, Binding, createLinkModal } from '../../core';
import { BaseFieldComponent } from './BaseField';

/**
 * An URL field.
 *
 * @extends BaseFieldComponent
 */
class URLComponent extends BaseFieldComponent {
	/**
	 * Create an input field.
	 */
	protected createInput(): void {
		const { name, id, value, placeholder, label } = this._data;
		const combo = create('div', [CSS.inputCombo], {}, this);
		const bindingName = `input_${id}`;
		const input = create(
			'input',
			[CSS.input],
			{
				id,
				name,
				value,
				type: 'text',
				placeholder,
			},
			combo
		);

		const button = create(
			'span',
			[CSS.inputComboButton],
			{},
			combo,
			'<i class="bi bi-link"></i>'
		);

		new Binding(bindingName, { input });

		listen(button, 'click', () => {
			createLinkModal(bindingName, label);
		});
	}
}

customElements.define('am-url', URLComponent);
