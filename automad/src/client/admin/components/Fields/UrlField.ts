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

import { CSS, create, Binding, createLinkModal, FieldTag } from '@/admin/core';
import { BaseFieldComponent } from './BaseField';

/**
 * An URL field.
 *
 * @extends BaseFieldComponent
 */
class UrlFieldComponent extends BaseFieldComponent {
	/**
	 * Create an input field.
	 */
	protected createInput(): void {
		const { name, id, value, placeholder, label } = this._data;
		const combo = create('div', [CSS.inputCombo], {}, this);
		const bindingName = `urlComponent_${id}`;
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
			this.hasAttribute('disabled') ? { disabled: '' } : {},
			combo,
			'<i class="bi bi-link"></i>'
		);

		new Binding(bindingName, { input });

		this.listen(button, 'click', () => {
			createLinkModal(bindingName, label);
		});
	}
}

customElements.define(FieldTag.url, UrlFieldComponent);
