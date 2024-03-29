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

import { FieldTag, createSelect, getPageURL, App } from '@/admin/core';
import { BaseFieldComponent } from './BaseField';
import themes from 'automad-prism-themes/dist/themes.json';

const defaultTheme = 'tokyo-night-storm';

/**
 * A syntax theme select field.
 *
 * @extends BaseFieldComponent
 */
class SyntaxThemeSelectComponent extends BaseFieldComponent {
	/**
	 * Render the input field.
	 */
	createInput(): void {
		const { name, id, value } = this._data;
		const isPage = !!getPageURL();
		const _default = isPage ? '' : defaultTheme;
		const _value = (value as string) || _default;

		let options = themes;

		if (isPage) {
			options = [
				{ text: App.text('useSharedDefault'), value: '' },
				...themes,
			];
		}

		createSelect(options, _value, this, name, id);
	}
}

customElements.define(FieldTag.syntaxSelect, SyntaxThemeSelectComponent);
