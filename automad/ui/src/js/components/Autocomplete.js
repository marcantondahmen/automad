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

import { BaseComponent } from './BaseComponent';
import { debounce, listen } from '../utils/core';
import { create } from '../utils/create';
import { requestController } from '../utils/request';

/**
 * <am-autocomplete controller="UI::autocompleteLink"></am-autocomplete>
 */

export class Autocomplete extends BaseComponent {
	items = [];
	itemsFiltered = [];
	selectedIndex = null;
	initialIndex = null;
	minInputLength = 1;

	static get observedAttributes() {
		return ['controller'];
	}

	connectedCallback() {
		this.input = create(
			'input',
			[this.cls.input, this.cls.inputLarge],
			{ type: 'text' },
			this
		);

		this.dropdown = create(
			'div',
			[this.cls.dropdown, this.cls.hidden],
			{},
			this
		);

		this.init();
	}

	async init() {
		const data = await requestController(this.elementAttributes.controller);

		if (typeof data.autocomplete === 'undefined') {
			return false;
		}

		data.autocomplete.forEach((item) => {
			this.items.push({
				element: this.createItemElement(item),
				value: this.createItemValue(item),
				item: item,
			});
		});

		this.itemsFiltered = this.items;

		this.registerDropdownEvents();
		this.registerInputEvents();
		this.update();
	}

	createItemElement(item) {
		const element = create('a', [this.cls.dropdownItem], {});

		element.innerHTML = `${item.title}`;

		return element;
	}

	createItemValue(item) {
		return item.value.toLowerCase();
	}

	renderDropdown() {
		this.dropdown.innerHTML = '';

		this.itemsFiltered.forEach((item) => {
			this.dropdown.appendChild(item.element);
		});
	}

	update() {
		const filters = this.input.value.toLowerCase().split(' ');

		this.itemsFiltered = [];

		this.items.forEach((item) => {
			var include = true;

			for (var i = 0; i < filters.length; i++) {
				if (item.value.indexOf(filters[i]) == -1) {
					include = false;
					break;
				}
			}

			if (include) {
				this.itemsFiltered.push(item);
			}
		});

		this.selectedIndex = this.initialIndex;
		this.renderDropdown();
		this.toggleActiveItemStyle();
	}

	onKeyDownEvent(event) {
		switch (event.keyCode) {
			case 38:
				this.prev();
				break;
			case 40:
				this.next();
				break;
			case 13:
				this.select(event);
				break;
			case 9:
			case 27:
				this.input.blur();
				this.close();
				break;
		}
	}

	onKeyUpEvent(event) {
		if (![38, 40, 13, 9, 27].includes(event.keyCode)) {
			this.update();
			this.open();
		}
	}

	onMouseOverEvent(event) {
		let item = event.target;

		if (!item.matches(`.${this.cls.dropdownItem}`)) {
			item = event.target.closest(`.${this.cls.dropdownItem}`);
		}

		this.selectedIndex = Array.from(this.dropdown.children).indexOf(item);

		this.toggleActiveItemStyle();
	}

	registerInputEvents() {
		listen(this.input, 'keydown', this.onKeyDownEvent.bind(this));
		listen(this.input, 'keyup', debounce(this.onKeyUpEvent.bind(this)));
		listen(this.input, 'focus', this.open.bind(this));

		listen(document, 'click', (event) => {
			if (!this.contains(event.target)) {
				this.close();
			}
		});
	}

	registerDropdownEvents() {
		listen(
			this.dropdown,
			'mouseover',
			(event) => {
				this.onMouseOverEvent(event);
			},
			`.${this.cls.dropdownItem}`
		);

		listen(
			this.dropdown,
			'click',
			(event) => {
				this.select(event);
			},
			`.${this.cls.dropdownItem}`
		);
	}

	toggleActiveItemStyle() {
		this.itemsFiltered.forEach((item, index) => {
			item.element.classList.toggle(
				this.cls.dropdownItemActive,
				index == this.selectedIndex
			);
		});
	}

	close() {
		this.selectedIndex = this.initialIndex;
		this.toggleActiveItemStyle();
		this.dropdown.classList.add(this.cls.hidden);
	}

	open() {
		if (this.input.value.length >= this.minInputLength) {
			this.update();
			this.dropdown.classList.remove(this.cls.hidden);
		}
	}

	prev() {
		this.selectedIndex--;

		if (this.selectedIndex < 0) {
			this.selectedIndex = this.itemsFiltered.length - 1;
		}

		this.toggleActiveItemStyle();
	}

	next() {
		if (this.selectedIndex === null) {
			this.selectedIndex = 0;
		} else {
			this.selectedIndex++;

			if (this.selectedIndex > this.itemsFiltered.length - 1) {
				this.selectedIndex = 0;
			}
		}

		this.toggleActiveItemStyle();
	}

	select(event) {
		event.preventDefault();

		if (this.selectedIndex !== null) {
			this.input.value = this.itemsFiltered[this.selectedIndex].value;
		}

		this.close();
	}
}

customElements.define('am-autocomplete', Autocomplete);
