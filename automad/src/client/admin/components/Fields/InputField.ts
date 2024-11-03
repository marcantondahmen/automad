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

import { create, CSS, FieldTag } from '@/admin/core';
import { BaseFieldComponent } from './BaseField';

/**
 * A standard input field with a label.
 *
 * @extends BaseFieldComponent
 */
export class InputFieldComponent extends BaseFieldComponent {
	/**
	 * The input type.
	 */
	protected get inputType(): string {
		return 'text';
	}

	/**
	 * Create an input field.
	 */
	protected createInput(): void {
		const { name, id, value, placeholder } = this._data;
		create(
			'input',
			[CSS.input],
			{ id, name, value, type: this.inputType, placeholder },
			this
		);
	}
}

customElements.define(FieldTag.input, InputFieldComponent);
