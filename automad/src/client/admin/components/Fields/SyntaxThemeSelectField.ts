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
	FieldTag,
	createSelect,
	getPageURL,
	App,
	CSS,
	create,
	html,
	Attr,
} from '@/admin/core';
import { BaseFieldComponent } from './BaseField';
import themes from 'automad-prism-themes/dist/themes.json';

const defaultTheme = 'automad.light-dark';

/**
 * A syntax theme select field.
 *
 * @extends BaseFieldComponent
 */
class SyntaxThemeSelectFieldComponent extends BaseFieldComponent {
	/**
	 * Render the input field.
	 */
	createInput(): void {
		const { name, id, value } = this._data;
		const isPage = !!getPageURL();
		const _default = isPage ? '' : defaultTheme;
		const _value = (value as string) || _default;

		let options = [
			{ text: '&mdash;', value: 'base' },
			...themes.filter((theme) => theme.value != 'base'),
		];

		if (isPage) {
			options = [
				{ text: App.text('useSharedDefault'), value: '' },
				...options,
			];
		}

		this.classList.add(CSS.flex, CSS.flexColumn, CSS.flexGap);
		createSelect(options, _value, this, name, id);
		create(
			'div',
			[],
			{},
			this,
			html`
				<a
					href="https://automadcms.github.io/automad-prism-themes/"
					target="_blank"
				>
					<am-icon-text
						${Attr.icon}="palette2"
						${Attr.text}="${App.text('visitSyntaxGallery')}"
					></am-icon-text>
				</a>
			`
		);
	}
}

customElements.define(FieldTag.syntaxSelect, SyntaxThemeSelectFieldComponent);
