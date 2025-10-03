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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { create, CSS, EventName, html, Undo } from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';
import { KeyValueMap, SelectComponentOption, UndoValue } from '@/admin/types';

/**
 * An advanced select component. The component inner HTML has to contain a single span
 * in order to reflect the selected value from the select element. Additional prefixes or suffixes can be added.
 *
 * @extends BaseComponent
 */
export class SelectComponent extends BaseComponent {
	/**
	 * The tag name for the component.
	 *
	 * @static
	 */
	static TAG_NAME = 'am-select';

	/**
	 * The value display container.
	 */
	private label: HTMLElement;

	/**
	 * The actual select element.
	 */
	private _select: HTMLSelectElement;

	/**
	 * The option setter.
	 */
	set options(options: SelectComponentOption[]) {
		let renderedOptions = '';

		options.forEach((option) => {
			renderedOptions += html`
				<option value="${option.value}">
					${typeof option.text !== 'undefined'
						? option.text
						: option.value}
				</option>
			`;
		});

		this._select.innerHTML = renderedOptions;

		setTimeout(this.updateLabel.bind(this), 0);
	}

	/**
	 * The current value of the select component.
	 */
	get value(): string {
		return this.selectedOption.value;
	}

	/**
	 * The value setter.
	 *
	 * @param value
	 */
	set value(value: string) {
		this._select.value = value;
		this.updateLabel();
	}

	/**
	 * Return the private select element.
	 */
	get select(): HTMLSelectElement {
		return this._select;
	}

	/**
	 * The currently selected option.
	 */
	get selectedOption(): HTMLOptionElement {
		return this._select.options[this._select.selectedIndex];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.select);
	}

	/**
	 * Update the value display label.
	 */
	private updateLabel(): void {
		try {
			this.label.textContent = this.selectedOption.text;
		} catch {}
	}

	/**
	 * Get the initial value and register the event listener.
	 */
	public init(
		options: SelectComponentOption[],
		selected: string,
		prefix: string,
		attributes: KeyValueMap
	): void {
		this.innerHTML = prefix;

		this.label = create('span', [], {}, this);
		this._select = create('select', [], attributes, this);

		this.options = options;
		this.value = selected;

		const toggleFocus = () => {
			this.classList.toggle(
				CSS.focus,
				this._select === document.activeElement
			);
		};

		this.listen(this._select, 'focus blur', toggleFocus);

		toggleFocus();

		this.listen(
			this._select,
			`change ${EventName.changeByBinding}`,
			this.updateLabel.bind(this)
		);

		setTimeout(() => {
			Undo.attach({
				getValueProvider: () => this._select,
				query: () => this._select.value,
				mutate: (value: UndoValue) => {
					this._select.value = `${value}`;
				},
			});
		}, 0);
	}
}

customElements.define(SelectComponent.TAG_NAME, SelectComponent);
