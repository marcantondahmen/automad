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
 * Copyright (c) 2021-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { AutocompleteItemData, KeyValueMap, PageMetaData } from '@/types';
import { App, create, html, Attr } from '@/core';
import { AutocompleteComponent } from './Autocomplete';

/**
 * Compile the autocompletion data.
 *
 * @returns the autocomplete data
 */
const autocompleteData = (): AutocompleteItemData[] => {
	const data: AutocompleteItemData[] = [];
	const pages: PageMetaData[] = Object.values(App.pages);

	pages.sort((a: KeyValueMap, b: KeyValueMap) =>
		a.lastModified < b.lastModified
			? 1
			: b.lastModified < a.lastModified
				? -1
				: 0
	);

	pages.forEach((page: PageMetaData) => {
		data.push({
			value: page.url,
			title: page.title,
		});
	});

	return data;
};

/**
 * An input field with page autocompletion.
 *
 * @example
 * <am-autocomplete-url></am-autocomplete-url>
 *
 * @extends AutocompleteComponent
 */
export class AutocompleteUrlComponent extends AutocompleteComponent {
	/**
	 * The autocomplete data.
	 */
	protected get data(): AutocompleteItemData[] {
		return autocompleteData();
	}

	/**
	 * Create a dropdown item element.
	 *
	 * @param item
	 * @returns the created element
	 */
	protected createItemElement(item: KeyValueMap): HTMLElement {
		return create(
			'a',
			[this.linkClass],
			{},
			null,
			html`
				<am-icon-text
					${Attr.icon}="link"
					${Attr.text}="$${item.title}"
				></am-icon-text>
			`
		);
	}
}

customElements.define('am-autocomplete-url', AutocompleteUrlComponent);
