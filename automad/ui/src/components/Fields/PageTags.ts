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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import Tagify from '@yaireo/tagify';
import { App, classes, create } from '../../core';
import { PageDataComponent } from '../Forms/PageData';
import { FieldComponent } from './Field';

/**
 * A tags input field.
 *
 * @extends FieldComponent
 */
class PageTagsComponent extends FieldComponent {
	/**
	 * Create the input field.
	 *
	 * @see {@link tagify https://github.com/yairEO/tagify}
	 */
	input(): void {
		const { name, id, value } = this._data;
		const textarea = create(
			'textarea',
			[classes.input],
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
		});

		tagify.on('change', (event: Event) => {
			const form: PageDataComponent = this.closest('am-page-data');

			form.onChange(textarea);
		});
	}
}

customElements.define('am-page-tags', PageTagsComponent);
