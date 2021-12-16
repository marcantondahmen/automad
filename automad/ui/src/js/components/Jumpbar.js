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

import { Autocomplete } from './Autocomplete';
import { create } from '../utils/create';

/**
 * <am-jumpbar controller="UI::autocompleteJump"></am-jumpbar>
 */

class Jumpbar extends Autocomplete {
	initialIndex = 0;
	minInputLength = 0;

	createItemElement(item) {
		const element = create('a', [this.cls.dropdownItem], {
			href: item.url,
		});

		element.innerHTML = this.itemHtml(item.icon, item.title, item.subtitle);

		return element;
	}

	createItemValue(item) {
		return item.value.toLowerCase();
	}

	itemHtml(icon, title, subtitle) {
		return `
			<i class="bi-${icon}"></i>&nbsp;
			${title}
			<span class="${this.cls.muted}">
				${subtitle}
			</span>
		`;
	}

	update() {
		const search = this.itemsFiltered[0];

		search.element.setAttribute(
			'href',
			`${search.item.url}&search=${encodeURIComponent(this.input.value)}`
		);

		search.value = `${search.value} ${this.input.value.toLowerCase()}`;
		search.element.innerHTML = this.itemHtml(
			search.item.icon,
			search.item.title,
			this.input.value
		);

		super.update();
	}

	select(event) {
		if (this.selectedIndex !== null) {
			this.itemsFiltered[this.selectedIndex].element.click();
		}

		this.close();
	}
}

customElements.define('am-jumpbar', Jumpbar);
