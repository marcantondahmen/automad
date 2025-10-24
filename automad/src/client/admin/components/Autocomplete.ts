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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { BaseComponent } from '@/admin/components/Base';
import {
	AutocompleteItem,
	AutocompleteItemData,
	KeyValueMap,
} from '@/admin/types';
import {
	App,
	create,
	debounce,
	html,
	EventName,
	CSS,
	fire,
	Attr,
} from '@/admin/core';

/**
 * An input field with page autocompletion.
 *
 * @example
 * <am-autocomplete name="..." ${Attr.data}="item1, item2" $Attr.min="2"></am-autocomplete>
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
		return ['placeholder', 'name', 'value'];
	}

	/**
	 * The autocomplete data.
	 */
	protected get data(): AutocompleteItemData[] {
		const data: AutocompleteItemData[] = [];
		const dataCsv = this.getAttribute(Attr.data) ?? '';

		dataCsv.split(',').forEach((item) => {
			const value = item.trim();

			data.push({ value, title: value });
		});

		return data;
	}

	/**
	 * Autocompletion items.
	 */
	protected items: AutocompleteItem[] = [];

	/**
	 * The filtered autocompletion items.
	 */
	protected itemsFiltered: AutocompleteItem[];

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
	protected get minInputLength() {
		return parseInt(this.getAttribute(Attr.min) ?? '1');
	}

	/**
	 * The maximum number of displayed items.
	 */
	protected maxItems = 8;

	/**
	 * The input element.
	 */
	public input: HTMLInputElement;

	/**
	 * The dropdown element.
	 */
	protected dropdown: HTMLDivElement;

	/**
	 * The dropdown items CSS class.
	 */
	protected elementClasses = [CSS.dropdown];

	/**
	 * The dropdown input CSS class.
	 */
	protected inputClasses = [CSS.input];

	/**
	 * The dropdown items CSS class.
	 */
	protected itemsClasses = [CSS.dropdownItems, CSS.dropdownItemsAutocomplete];

	/**
	 * The link class.
	 */
	protected linkClass = CSS.dropdownLink;

	/**
	 * The active link class.
	 */
	protected linkActiveClass = CSS.active;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(...this.elementClasses);

		this.listen(window, EventName.appStateChange, this.init.bind(this));

		this.init();
	}

	/**
	 * Init the autocompletion.
	 */
	private init(): void {
		if (typeof this.data !== 'undefined') {
			this.innerHTML = '';
			this.items = [];

			this.input = this.createInput();

			if (!this.hasAttribute('disabled')) {
				this.dropdown = create('div', this.itemsClasses, {}, this);

				this.data.forEach((item: KeyValueMap) => {
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
	}

	/**
	 * Create the main input element.
	 *
	 * @returns the input element
	 */
	protected createInput(): HTMLInputElement {
		const disabled = this.hasAttribute('disabled') ? { disabled: '' } : {};
		const attributes: KeyValueMap = { type: 'text', ...disabled };
		const placeholder: string = App.text(
			this.elementAttributes.placeholder
		);

		if (placeholder) {
			attributes['placeholder'] = placeholder;
		}

		if (this.elementAttributes.name) {
			attributes['name'] = this.elementAttributes.name;
		}

		if (this.elementAttributes.value) {
			attributes['value'] = this.elementAttributes.value;
		}

		return create('input', this.inputClasses, attributes, this);
	}

	/**
	 * Create a dropdown item element.
	 *
	 * @param item
	 * @returns the created element
	 */
	protected createItemElement(item: KeyValueMap): HTMLElement {
		return create('a', [this.linkClass], {}, null, html`$${item.title}`);
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
		let numIncluded = 0;

		this.itemsFiltered = [];

		this.items.forEach((item) => {
			var include = true;

			if (numIncluded < this.maxItems) {
				for (var i = 0; i < filters.length; i++) {
					if (item.value.indexOf(filters[i]) == -1) {
						include = false;
						break;
					}
				}

				if (include) {
					this.itemsFiltered.push(item);
					numIncluded++;
				}
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
				this.prev(event);
				break;
			case 40:
				this.next(event);
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

		if (!target.matches(`.${this.linkClass}`)) {
			target = target.closest(`.${this.linkClass}`);
		}

		this.selectedIndex = Array.from(this.dropdown.children).indexOf(target);

		this.toggleActiveItemStyle();
	}

	/**
	 * Register events for the input field.
	 */
	protected registerInputEvents(): void {
		this.listen(this.input, 'keydown', this.onKeyDownEvent.bind(this));

		this.listen(
			this.input,
			'keyup',
			debounce(this.onKeyUpEvent.bind(this))
		);

		this.listen(this.input, 'focus', this.open.bind(this));

		this.listen(document, 'click', (event: MouseEvent) => {
			if (!this.contains(event.target as Element)) {
				this.close();
			}
		});
	}

	/**
	 * Register events for the dropdown.
	 */
	protected registerDropdownEvents(): void {
		this.listen(
			this.dropdown,
			'mouseover',
			(event: MouseEvent) => {
				this.onMouseOverEvent(event);
			},
			`.${this.linkClass}`
		);

		this.listen(
			this.dropdown,
			'click',
			(event: MouseEvent) => {
				this.select(event);
			},
			`.${this.linkClass}`
		);
	}

	/**
	 * Toggle item styles in order to highlight the active item.
	 */
	protected toggleActiveItemStyle(): void {
		this.itemsFiltered.forEach((item, index) => {
			item.element.classList.toggle(
				this.linkActiveClass,
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
		this.classList.remove(CSS.dropdownOpen);
	}

	/**
	 * Open the dropdown.
	 */
	open(): void {
		if (this.input.value.length >= this.minInputLength) {
			this.update();
			this.classList.add(CSS.dropdownOpen);
		}
	}

	/**
	 * Highlight and save the index of the previous item in the dropdown.
	 */
	prev(event: Event): void {
		event.stopImmediatePropagation();
		event.preventDefault();

		this.selectedIndex--;

		if (this.selectedIndex < 0) {
			this.selectedIndex = this.itemsFiltered.length - 1;
		}

		this.toggleActiveItemStyle();
	}

	/**
	 * Highlight and save the index of the next item in the dropdown.
	 */
	next(event: Event): void {
		event.stopImmediatePropagation();
		event.preventDefault();

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
		event.stopImmediatePropagation();
		event.preventDefault();

		if (this.selectedIndex !== null) {
			this.input.value = this.itemsFiltered[this.selectedIndex].value;
		}

		this.close();

		fire(EventName.autocompleteSelect, this);
	}
}

customElements.define('am-autocomplete', AutocompleteComponent);
