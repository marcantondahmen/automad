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
 * The Jumpbar field element.
 * ```
 * <am-jumpbar></am-jumpbar>
 * ```
 *
 * @extends Autocomplete
 */
class Jumpbar extends Autocomplete {
	/**
	 * The controller.
	 *
	 * @type {string}
	 */
	controller = 'UIController::autocompleteJump';

	/**
	 * The initial index.
	 *
	 * @type {(null|number)}
	 */
	initialIndex = 0;

	/**
	 * The minimum input value length to trigger the dropdown.
	 *
	 * @type {number}
	 */
	minInputLength = 0;

	/**
	 * Create a dropdown item.
	 *
	 * @param {Object} item
	 * @returns {HTMLElement} the dropdown item element
	 */
	createItemElement(item) {
		const element = create('a', [this.cls.dropdownItem], {
			href: item.url,
		});

		element.innerHTML = this.itemHtml(item.icon, item.title, item.subtitle);

		return element;
	}

	/**
	 * Create a value for a dropdown item.
	 *
	 * @param {Object} item
	 * @returns {string} the item value
	 */
	createItemValue(item) {
		return item.value.toLowerCase();
	}

	/**
	 * Create the inner HTML for a dropdown item.
	 *
	 * @param {string} icon
	 * @param {string} title
	 * @param {string} subtitle
	 * @returns {string} the HTML string
	 */
	itemHtml(icon, title, subtitle) {
		return `
			<i class="bi bi-${icon}"></i>
			<span>${title}</span>
			<span class="${this.cls.muted}">
				${subtitle}
			</span>
		`;
	}

	/**
	 * Update the dropdown and the list of filtered items based on the current input value.
	 */
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

	/**
	 * Select an item and use the item value as the input value.
	 *
	 * @param {Event} event
	 */
	select(event) {
		if (this.selectedIndex !== null) {
			this.itemsFiltered[this.selectedIndex].element.click();
		}

		this.close();
	}
}

customElements.define('am-jumpbar', Jumpbar);
