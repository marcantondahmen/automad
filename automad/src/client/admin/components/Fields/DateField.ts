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
 * Copyright (c) 2021-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { create, CSS, FieldTag, fire } from '@/admin/core';
import { BaseFieldComponent } from './BaseField';

/**
 * A date field.
 *
 * @extends BaseFieldComponent
 */
class DateFieldComponent extends BaseFieldComponent {
	/**
	 * Create an input field.
	 */
	createInput(): void {
		const { name, id, value, placeholder } = this._data;
		const date = (value as string).match(/[\d-]+T\d\d:\d\d/)?.[0] ?? '';

		const input = create(
			'input',
			[CSS.input],
			{ id, name, value: date, type: 'datetime-local', placeholder },
			this
		);

		this.listen(input, 'keyup', () => {
			if (!input.value) {
				this.mutate(null);
				fire('change', this);
			}
		});
	}
}

customElements.define(FieldTag.date, DateFieldComponent);
