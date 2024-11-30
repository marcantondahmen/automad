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

import Tagify from '@yaireo/tagify';
import { App, create, CSS, debounce, FieldTag, State } from '@/admin/core';
import { PageDataFormComponent } from '@/admin/components/Forms/PageDataForm';
import { BaseFieldComponent } from './BaseField';

/**
 * A tags input field.
 *
 * @extends BaseFieldComponent
 */
class PageTagsFieldComponent extends BaseFieldComponent {
	/**
	 * Create the input field.
	 *
	 * @see {@link tagify https://github.com/yairEO/tagify}
	 */
	createInput(): void {
		const { name, id, value } = this._data;
		const textarea = create(
			'textarea',
			[CSS.input],
			{
				name,
				id,
			},
			this
		);

		textarea.innerHTML = value;

		const tagify = new Tagify(textarea, {
			whitelist: App.tags,
			originalInputValueFormat: (tags) =>
				tags.map((item) => item.value).join(', '),
			dropdown: {
				enabled: 0,
				maxItems: 8,
				position: 'text',
				closeOnSelect: true,
			},
		});

		tagify.on('change', (event: Event) => {
			const form: PageDataFormComponent =
				this.closest('am-page-data-form');

			form.onChange();
		});

		// Update global tags in the app state.
		tagify.on(
			'change',
			debounce(() => {
				const state = State.getInstance();
				const fieldTags = textarea.value
					.split(',')
					.map((tag: string) => tag.trim());

				state.set(
					'tags',
					[...new Set([...state.get('tags'), ...fieldTags])].sort()
				);
			}, 500)
		);
	}
}

customElements.define(FieldTag.pageTags, PageTagsFieldComponent);
