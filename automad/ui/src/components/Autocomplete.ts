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

import { BaseComponent } from './Base';
import { classes, debounce, listen } from '../utils/core';
import { create } from '../utils/create';
import { requestController } from '../utils/request';
import { KeyValueMap } from '../utils/types';

export interface Item {
	element: HTMLElement;
	value: string;
	item: KeyValueMap;
}

/**
 * An input field with page autocompletion.
 *
 * @example
 * <am-autocomplete></am-autocomplete>
 *
 * @extends BaseComponent
 */
export class AutocompleteComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['placeholder'];
	}

	/**
	 * The controller.
	 */
	protected controller = 'UIController::autocompleteLink';

	/**
	 * Autocompletion items.
	 */
	protected items: Item[] = [];

	/**
	 * The filtered autocompletion items.
	 */
	protected itemsFiltered: Item[];

	/**
	 * The selected index.
	 */
	protected selectedIndex: number | null = null;

	/**
	 * The initial index.
	 */
	protected initialIndex: number | null = null;

	/**
	 * The minimum input value length to trigger the dropdown.
	 */
	protected minInputLength = 1;

	/**
	 * The input element.
	 */
	protected input: HTMLInputElement;

	/**
	 * The dropdown element.
	 */
	protected dropdown: HTMLDivElement;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const placeholder: string = this.elementAttributes.placeholder || '';
		this.classList.add(classes.dropdown, classes.dropdownForm);

		this.input = create(
			'input',
			[classes.input],
			{ type: 'text', placeholder },
			this
		);

		this.dropdown = create(
			'div',
			[classes.dropdownItems, classes.dropdownItemsFullWidth],
			{},
			this
		);

		this.init();
	}

	/**
	 * Init the autocompletion.
	 *
	 * @returns a Promise
	 * @async
	 */
	private async init(): Promise<void> {
		const response = await requestController(this.controller);

		if (typeof response.data !== 'undefined') {
			response.data.forEach((item: KeyValueMap) => {
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
	}

	/**
	 * Create a dropdown item element.
	 *
	 * @param item
	 * @returns the created element
	 */
	protected createItemElement(item: KeyValueMap): HTMLElement {
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
	 * @param item
	 * @returns the value
	 */
	protected createItemValue(item: KeyValueMap): string {
		return item.value.toLowerCase();
	}

	/**
	 * Render the dropdown.
	 */
	protected renderDropdown(): void {
		this.dropdown.innerHTML = '';

		this.itemsFiltered.forEach((item) => {
			this.dropdown.appendChild(item.element);
		});
	}

	/**
	 * Update the dropdown and the list of filtered items based on the current input value.
	 */
	protected update(): void {
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
	 * @param event
	 */
	protected onKeyDownEvent(event: KeyboardEvent): void {
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
	 * @param event
	 */
	protected onKeyUpEvent(event: KeyboardEvent): void {
		if (![38, 40, 13, 9, 27].includes(event.keyCode)) {
			this.update();
			this.open();
		}
	}

	/**
	 * Handle mouse over events.
	 *
	 * @param event
	 */
	protected onMouseOverEvent(event: MouseEvent): void {
		let target = event.target as HTMLElement;

		if (!target.matches(`.${classes.dropdownItem}`)) {
			target = target.closest(`.${classes.dropdownItem}`);
		}

		this.selectedIndex = Array.from(this.dropdown.children).indexOf(target);

		this.toggleActiveItemStyle();
	}

	/**
	 * Register events for the input field.
	 */
	protected registerInputEvents(): void {
		listen(this.input, 'keydown', this.onKeyDownEvent.bind(this));
		listen(this.input, 'keyup', debounce(this.onKeyUpEvent.bind(this)));
		listen(this.input, 'focus', this.open.bind(this));

		listen(document, 'click', (event: MouseEvent) => {
			if (!this.contains(event.target as Element)) {
				this.close();
			}
		});
	}

	/**
	 * Register events for the dropdown.
	 */
	protected registerDropdownEvents(): void {
		listen(
			this.dropdown,
			'mouseover',
			(event: MouseEvent) => {
				this.onMouseOverEvent(event);
			},
			`.${classes.dropdownItem}`
		);

		listen(
			this.dropdown,
			'click',
			(event: MouseEvent) => {
				this.select(event);
			},
			`.${classes.dropdownItem}`
		);
	}

	/**
	 * Toggle item styles in order to highlight the active item.
	 */
	protected toggleActiveItemStyle(): void {
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
	close(): void {
		this.selectedIndex = this.initialIndex;
		this.toggleActiveItemStyle();
		this.classList.remove(classes.dropdownOpen);
	}

	/**
	 * Open the dropdown.
	 */
	open(): void {
		if (this.input.value.length >= this.minInputLength) {
			this.update();
			this.classList.add(classes.dropdownOpen);
		}
	}

	/**
	 * Highlight and save the index of the previous item in the dropdown.
	 */
	prev(): void {
		this.selectedIndex--;

		if (this.selectedIndex < 0) {
			this.selectedIndex = this.itemsFiltered.length - 1;
		}

		this.toggleActiveItemStyle();
	}

	/**
	 * Highlight and save the index of the next item in the dropdown.
	 */
	next(): void {
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
	 * @param event
	 */
	select(event: Event): void {
		event.preventDefault();

		if (this.selectedIndex !== null) {
			this.input.value = this.itemsFiltered[this.selectedIndex].value;
		}

		this.close();
	}
}

customElements.define('am-autocomplete', AutocompleteComponent);
