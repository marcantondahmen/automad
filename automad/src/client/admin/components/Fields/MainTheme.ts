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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, create, CSS, html, Route } from '../../core';
import { BaseFieldComponent } from './BaseField';
import { Theme } from '../../types';

/**
 * A theme select field.
 *
 * @extends BaseFieldComponent
 */
export class MainThemeComponent extends BaseFieldComponent {
	/**
	 * Create the actual input field.
	 */
	protected createInput(): void {
		const { name, id, value } = this._data;
		const select = create('am-select', [], {}, this);
		const selectedTheme = App.themes[value as string] as Theme;
		const options = (): string => {
			const themes = Object.values(App.themes) as Theme[];
			const options: string[] = [];

			themes.forEach((theme) => {
				const selected =
					selectedTheme.path === theme.path ? 'selected' : '';

				options.push(
					html`<option value="${theme.path}" ${selected}>
						${theme.name}
					</option>`
				);
			});

			return options.join('');
		};

		select.innerHTML = html`
			<span>${selectedTheme.name}</span>
			<select id=${id} name=${name}>
				${options()}
			</select>
		`;

		create(
			'am-icon-text',
			[],
			{ [Attr.icon]: 'box-seam', [Attr.text]: App.text('moreThemes') },
			create(
				'am-link',
				[CSS.textLink],
				{ [Attr.target]: Route.packages },
				this
			)
		);
	}
}

customElements.define('am-main-theme', MainThemeComponent);
