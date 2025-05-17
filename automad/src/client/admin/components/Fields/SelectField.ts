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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	createSelect,
	CSS,
	FieldTag,
	getPageURL,
	listen,
} from '@/admin/core';
import { BaseFieldComponent } from './BaseField';

/**
 * A select field with a label.
 *
 * @extends BaseFieldComponent
 */
class SelectFieldComponent extends BaseFieldComponent {
	/**
	 * Create an input field.
	 */
	createInput(): void {
		const { name, id, value, options, placeholder } = this._data;
		const opt = Object.keys(options).map((key: string) => ({
			value: key,
			text: options[key],
		}));

		const select = createSelect(
			[
				{
					text: !!getPageURL()
						? `${App.text('useSharedDefault')} ${!!placeholder ? `(${options[String(placeholder)]})` : ''}`
						: '&mdash;',
					value: '',
				},
				...opt,
			],
			`${value}`,
			this,
			name,
			id,
			'<i class="bi bi-ui-radios"></i> ',
			!!value ? [] : [CSS.textMuted]
		);

		listen(select, 'change', () => {
			select.classList.toggle(CSS.textMuted, !this.input.value);
		});
	}
}

customElements.define(FieldTag.select, SelectFieldComponent);
