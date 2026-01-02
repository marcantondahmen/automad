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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	create,
	createSelect,
	CSS,
	FieldTag,
	html,
	query,
	Route,
	uniqueId,
} from '@/admin/core';
import { SelectComponentOption, Theme } from '@/admin/types';
import { ModalComponent } from '@/admin/components/Modal/Modal';
import { BaseFieldComponent } from '@/admin/components/Fields/BaseField';

/**
 * A theme select field.
 *
 * @extends BaseFieldComponent
 */
class MainThemeFieldComponent extends BaseFieldComponent {
	/**
	 * Create the actual input field.
	 */
	protected createInput(): void {
		const { name, id, value, label } = this._data;
		const themes = Object.values(App.themes) as Theme[];
		const selectedTheme =
			(App.themes[value as string] as Theme) ??
			Object.values(App.themes)[0];
		const options: SelectComponentOption[] = [];

		themes.forEach((theme) => {
			options.push({ text: theme.name, value: theme.path });
		});

		const modalId = uniqueId();
		const modal = create(
			ModalComponent.TAG_NAME,
			[],
			{ id: modalId },
			this,
			html`
				<am-modal-dialog>
					<am-modal-header>${label}</am-modal-header>
					<am-modal-body></am-modal-body>
				</am-modal-dialog>
			`
		) as ModalComponent;

		createSelect(
			options,
			selectedTheme.path,
			query('am-modal-body', modal),
			name,
			id,
			'<i class="bi bi-box-seam"></i>'
		);

		create(
			'am-modal-toggle',
			[
				CSS.input,
				CSS.flex,
				CSS.flexAlignCenter,
				CSS.flexGap,
				CSS.cursorPointer,
			],
			{ [Attr.modal]: `#${modalId}` },
			this,
			`<i class="bi bi-box-seam"></i>${selectedTheme.name}`
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

customElements.define(FieldTag.mainTheme, MainThemeFieldComponent);
