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
 * Copyright (c) 2021-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { BaseComponent } from './Base';
import {
	AutocompleteItem,
	AutocompleteItemData,
	KeyValueMap,
	PageMetaData,
} from '../types';
import {
	App,
	create,
	listen,
	debounce,
	html,
	eventNames,
	CSS,
	fire,
	Attr,
} from '../core';

/**
 * Compile the autocompletion data.
 *
 * @returns the autocomplete data
 */
const autocompleteData = (): AutocompleteItemData[] => {
	const data: AutocompleteItemData[] = [];
	const pages: PageMetaData[] = Object.values(App.pages);

	pages.sort((a: KeyValueMap, b: KeyValueMap) =>
		a.mTime < b.mTime ? 1 : b.mTime < a.mTime ? -1 : 0
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
	 * The autocomplete data.
	 */
	protected get data(): AutocompleteItemData[] {
		return autocompleteData();
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
	protected minInputLength = 1;

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
	protected elementClasses = [CSS.dropdown, CSS.dropdownForm];

	/**
	 * The dropdown input CSS class.
	 */
	protected inputClasses = [CSS.input];

	/**
	 * The dropdown items CSS class.
	 */
	protected itemsClasses = [CSS.dropdownItems];

	/**
	 * The link class.
	 */
	protected linkClass = CSS.dropdownLink;

	/**
	 * The active link class.
	 */
	protected linkActiveClass = CSS.dropdownLinkActive;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(...this.elementClasses);

		this.listeners.push(
			listen(window, eventNames.appStateChange, this.init.bind(this))
		);

		this.init();
	}

	/**
	 * Init the autocompletion.
	 *
	 * @returns a Promise
	 */
	private init(): void {
		if (typeof this.data !== 'undefined') {
			this.innerHTML = '';
			this.items = [];

			this.input = this.createInput();

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

	/**
	 * Create the main input element.
	 *
	 * @returns the input element
	 */
	protected createInput(): HTMLInputElement {
		const placeholder: string = App.text(
			this.elementAttributes.placeholder
		);

		return create(
			'input',
			this.inputClasses,
			{ type: 'text', placeholder },
			this
		);
	}

	/**
	 * Create a dropdown item element.
	 *
	 * @param item
	 * @returns the created element
	 */
	protected createItemElement(item: KeyValueMap): HTMLElement {
		const element = create('a', [this.linkClass], {});

		element.innerHTML = html`
			<am-icon-text
				${Attr.icon}="link"
				${Attr.text}="${item.title}"
			></am-icon-text>
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
			`.${this.linkClass}`
		);

		listen(
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

		fire(eventNames.autocompleteSelect, this);
	}
}

customElements.define('am-autocomplete', AutocompleteComponent);
