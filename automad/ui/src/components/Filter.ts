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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, classes, create, debounce, listen, queryAll } from '../core';
import { InputElement } from '../types';
import { BaseComponent } from './Base';
import { switcherChangeEventName } from './Switcher/Switcher';

/**
 * A field and card filter input component.
 *
 * @extends BaseComponent
 */
class FilterComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['placeholder'];
	}

	/**
	 * The target selector.
	 */
	protected get targetSelector() {
		return `[api] .${classes.field}:not(am-title), [api] .${classes.card}`;
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const input = this.render();

		listen(
			input,
			'input',
			debounce(() => {
				this.filter(input);
			}, 200)
		);

		this.listeners.push(
			listen(window, switcherChangeEventName, () => {
				input.value = '';
				this.filter(input);
			})
		);
	}

	/**
	 * Filter fields and cards based on the input value.
	 *
	 * @param input
	 */
	private filter(input: InputElement): void {
		const filters = input.value.toLowerCase().split(' ');
		const items = queryAll(this.targetSelector) as HTMLElement[];

		items.forEach((item) => {
			var hide = false;

			for (var i = 0; i < filters.length; i++) {
				if (item.textContent.toLowerCase().indexOf(filters[i]) == -1) {
					hide = true;
					break;
				}
			}

			item.classList.toggle(classes.displayNone, hide);
		});
	}

	/**
	 * Render the filter input.
	 *
	 * @return the filter input element
	 */
	private render(): InputElement {
		const placeholder = App.text(this.elementAttributes.placeholder);
		const input = create('input', [classes.input], { placeholder }, this);

		return input;
	}
}

customElements.define('am-filter', FilterComponent);
