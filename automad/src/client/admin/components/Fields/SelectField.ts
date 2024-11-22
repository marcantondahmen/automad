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
 * Copyright (c) 2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, createSelect, FieldTag, getPageURL } from '@/admin/core';
import { BaseFieldComponent } from './BaseField';

/**
 * A select field with a label.
 *
 * @extends BaseFieldComponent
 */
class SelectFieldComponent extends BaseFieldComponent {
	createInput(): void {
		const { name, id, value, options } = this._data;
		const opt = Object.keys(options).map((key: string) => ({
			value: key,
			text: options[key],
		}));

		createSelect(
			[
				{
					text: !!getPageURL()
						? App.text('useSharedDefault')
						: '&mdash;',
					value: '',
				},
				...opt,
			],
			`${value}`,
			this,
			name,
			id,
			'<i class="bi bi-ui-radios"></i> '
		);
	}
}

customElements.define(FieldTag.select, SelectFieldComponent);
