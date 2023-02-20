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
 * Copyright (c) 2022-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { create, CSS, EventName, html, listen, query } from '../core';
import { KeyValueMap, SelectComponentOption } from '../types';
import { BaseComponent } from './Base';

/**
 * An advanced select component. The component inner HTML has to contain a single span
 * in order to reflect the selected value from the select element. Additional prefixes or suffixes can be added.
 *
 * @example
 * <am-select>
 *     Some prefix text
 *     <span></span>
 *     <select>
 *         <option>...</option>
 *         <option>...</option>
 *     </select>
 * </am-select>
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
	 * The current value of the select component.
	 */
	get value(): string {
		return this.selectedOption.value;
	}

	/**
	 * Return the inner select element.
	 */
	get select(): HTMLSelectElement {
		return query('select', this) as HTMLSelectElement;
	}

	/**
	 * The currently selected option.
	 */
	get selectedOption(): HTMLOptionElement {
		return this.select.options[this.select.selectedIndex];
	}

	/**
	 * Create a select component based on an options object.
	 *
	 * @param options
	 * @param selected
	 * @param parent
	 * @param name
	 * @param id
	 * @param prefix
	 * @param cls
	 * @param attributes
	 * @returns the created component
	 */
	static create(
		options: SelectComponentOption[],
		selected: string,
		parent: HTMLElement = null,
		name: string = '',
		id: string = '',
		prefix: string = '',
		cls: string[] = [],
		attributes: KeyValueMap = {}
	): SelectComponent {
		const select = create(SelectComponent.TAG_NAME, cls, {}, parent);
		let renderedOptions = '';
		let renderedAttributes = '';

		options.forEach((option) => {
			renderedOptions += html`
				<option
					value="${option.value}"
					${selected === option.value ? 'selected' : ''}
				>
					${typeof option.text !== 'undefined'
						? option.text
						: option.value}
				</option>
			`;
		});

		for (const [key, value] of Object.entries(attributes)) {
			renderedAttributes += html` ${key}="${value}"`;
		}

		select.innerHTML = html`
			${prefix}
			<span class="${CSS.flexItemGrow}"></span>
			<select name="${name}" id="${id}" ${renderedAttributes}>
				${renderedOptions}
			</select>
		`;

		return select;
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.select);

		setTimeout(this.init.bind(this), 0);
	}

	/**
	 * Get the initial value and register the event listener.
	 */
	private init(): void {
		const span = query('span', this);
		const select = this.select;
		const update = () => {
			try {
				span.textContent = this.selectedOption.text;
			} catch {}
		};
		const toggleFocus = () => {
			this.classList.toggle(CSS.focus, select === document.activeElement);
		};

		listen(select, `change ${EventName.changeByBinding}`, update);
		listen(select, 'focus blur', toggleFocus);

		update();
		toggleFocus();
	}
}

customElements.define(SelectComponent.TAG_NAME, SelectComponent);
