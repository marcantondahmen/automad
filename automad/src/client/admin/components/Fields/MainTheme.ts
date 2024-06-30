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
 * Copyright (c) 2023-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, create, createSelect, CSS, FieldTag, Route } from '@/admin/core';
import { BaseFieldComponent } from './BaseField';
import { SelectComponentOption, Theme } from '@/admin/types';

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
		const themes = Object.values(App.themes) as Theme[];
		const selectedTheme =
			(App.themes[value as string] as Theme) ??
			Object.values(App.themes)[0];
		const options: SelectComponentOption[] = [];

		themes.forEach((theme) => {
			options.push({ text: theme.name, value: theme.path });
		});

		createSelect(
			options,
			selectedTheme.path,
			this,
			name,
			id,
			'<i class="bi bi-box-seam"></i>'
		);

		const links = create('div', [CSS.flex, CSS.flexColumn], {}, this);

		create(
			'am-icon-text',
			[],
			{
				[Attr.icon]: 'file-earmark-text',
				[Attr.text]: App.text('themeReadme'),
			},
			create(
				'a',
				[],
				{ href: selectedTheme.readme, target: '_blank' },
				links
			)
		);

		create(
			'am-icon-text',
			[],
			{
				[Attr.icon]: 'cloud-download',
				[Attr.text]: App.text('moreThemes'),
			},
			create(
				'am-link',
				[CSS.textLink],
				{ [Attr.target]: Route.packages },
				links
			)
		);
	}
}

customElements.define(FieldTag.mainTheme, MainThemeComponent);
