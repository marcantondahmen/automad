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

import { App, createSelect, CSS, FieldTag, getPageURL } from '@/admin/core';
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

		// Use this fallback when a theme is used that doesn't support this select field
		// and therefore doesn't provide valid options.
		const fallback = !!value
			? [
					{
						value:
							typeof value === 'string'
								? value
								: JSON.stringify(value),
					},
				]
			: [];

		const opt = !!options
			? Object.keys(options).map((key: string) => ({
					value: key,
					text: options[key],
				}))
			: fallback;

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

		this.listen(select, 'change', () => {
			select.classList.toggle(CSS.textMuted, !this.input.value);
		});
	}
}

customElements.define(FieldTag.select, SelectFieldComponent);
