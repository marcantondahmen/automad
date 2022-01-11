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
import { classes, debounce, listen } from '../utils/core';
import { create } from '../utils/create';
import { requestController } from '../utils/request';

/**
 * An input field with page autocompletion.
 * ```
 * <am-autocomplete></am-autocomplete>
 * ```
 *
 * @extends BaseComponent
 */
export class Autocomplete extends BaseComponent {
	/**
	 * The controller.
	 *
	 * @type {string}
	 */
	controller = 'UIController::autocompleteLink';

	/**
	 * Autocompletion items.
	 *
	 * @type {Array}
	 */
	items = [];

	/**
	 * The filtered autocompletion items.
	 *
	 * @type {Array}
	 */
	itemsFiltered = [];

	/**
	 * The selected index.
	 *
	 * @type {(null|number)}
	 */
	selectedIndex = null;

	/**
	 * The initial index.
	 *
	 * @type {(null|number)}
	 */
	initialIndex = null;

	/**
	 * The minimum input value length to trigger the dropdown.
	 *
	 * @type {number}
	 */
	minInputLength = 1;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		this.input = create(
			'input',
			[classes.input, classes.inputLarge],
			{ type: 'text' },
			this
		);

		this.dropdown = create(
			'div',
			[classes.dropdown, classes.hidden],
			{},
			this
		);

		this.init();
	}

	/**
	 * Init the autocompletion.
	 *
	 * @returns {boolean|null}
	 * @async
	 */
	async init() {
		const data = await requestController(this.controller);

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

	/**
	 * Create a dropdown item element.
	 *
	 * @param {Object} item
	 * @returns {HTMLElement}
	 */
	createItemElement(item) {
		const element = create('a', [classes.dropdownItem], {});

		element.innerHTML = `
			<i class="bi bi-link"></i>
			<span>${item.title}</span>
		`;

		return element;
	}

	/**
	 * Create an item value.
	 *
	 * @param {Object} item
	 * @returns {string}
	 */
	createItemValue(item) {
		return item.value.toLowerCase();
	}

	/**
	 * Render the dropdown.
	 */
	renderDropdown() {
		this.dropdown.innerHTML = '';

		this.itemsFiltered.forEach((item) => {
			this.dropdown.appendChild(item.element);
		});
	}

	/**
	 * Update the dropdown and the list of filtered items based on the current input value.
	 */
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

	/**
	 * Handle key down events.
	 *
	 * @param {Event} event
	 */
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

	/**
	 * Handle key up events.
	 *
	 * @param {Event} event
	 */
	onKeyUpEvent(event) {
		if (![38, 40, 13, 9, 27].includes(event.keyCode)) {
			this.update();
			this.open();
		}
	}

	/**
	 * Handle mouse over events.
	 *
	 * @param {Event} event
	 */
	onMouseOverEvent(event) {
		let item = event.target;

		if (!item.matches(`.${classes.dropdownItem}`)) {
			item = event.target.closest(`.${classes.dropdownItem}`);
		}

		this.selectedIndex = Array.from(this.dropdown.children).indexOf(item);

		this.toggleActiveItemStyle();
	}

	/**
	 * Register events for the input field.
	 */
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

	/**
	 * Register events for the dropdown.
	 */
	registerDropdownEvents() {
		listen(
			this.dropdown,
			'mouseover',
			(event) => {
				this.onMouseOverEvent(event);
			},
			`.${classes.dropdownItem}`
		);

		listen(
			this.dropdown,
			'click',
			(event) => {
				this.select(event);
			},
			`.${classes.dropdownItem}`
		);
	}

	/**
	 * Toggle item styles in order to highlight the active item.
	 */
	toggleActiveItemStyle() {
		this.itemsFiltered.forEach((item, index) => {
			item.element.classList.toggle(
				classes.dropdownItemActive,
				index == this.selectedIndex
			);
		});
	}

	/**
	 * Close the dropdown.
	 */
	close() {
		this.selectedIndex = this.initialIndex;
		this.toggleActiveItemStyle();
		this.dropdown.classList.add(classes.hidden);
	}

	/**
	 * Open the dropdown.
	 */
	open() {
		if (this.input.value.length >= this.minInputLength) {
			this.update();
			this.dropdown.classList.remove(classes.hidden);
		}
	}

	/**
	 * Highlight and save the index of the previous item in the dropdown.
	 */
	prev() {
		this.selectedIndex--;

		if (this.selectedIndex < 0) {
			this.selectedIndex = this.itemsFiltered.length - 1;
		}

		this.toggleActiveItemStyle();
	}

	/**
	 * Highlight and save the index of the next item in the dropdown.
	 */
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

	/**
	 * Select an item and use the item value as the input value.
	 *
	 * @param {Event} event
	 */
	select(event) {
		event.preventDefault();

		if (this.selectedIndex !== null) {
			this.input.value = this.itemsFiltered[this.selectedIndex].value;
		}

		this.close();
	}
}

customElements.define('am-autocomplete', Autocomplete);
