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
 * Copyright (c) 2022-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	create,
	CSS,
	debounce,
	EventName,
	keyCombo,
	listen,
	queryAll,
} from '@/admin/core';
import { InputElement } from '@/admin/types';
import { BaseComponent } from '@/admin/components/Base';

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
	protected get targetSelector(): string {
		return `section [${Attr.api}] .${CSS.field}:not(am-title-field), .${CSS.card}`;
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const input = this.render();

		this.classList.add(CSS.filter);

		listen(
			input,
			'input',
			debounce(() => {
				this.filter(input);
			}, 200)
		);

		this.addListener(
			listen(window, EventName.switcherChange, () => {
				input.value = '';
				this.filter(input);
			})
		);

		this.addListener(
			keyCombo('k', () => {
				input.focus();
			})
		);

		listen(input, 'keydown', (event: KeyboardEvent) => {
			if (event.keyCode == 27) {
				input.blur();
			}
		});
	}

	/**
	 * Filter fields and cards based on the input value.
	 *
	 * @param input
	 */
	private filter(input: InputElement): void {
		const filters = input.value.toLowerCase().split(' ');
		const items = queryAll(this.targetSelector);

		items.forEach((item) => {
			var hide = false;

			for (var i = 0; i < filters.length; i++) {
				if (item.textContent.toLowerCase().indexOf(filters[i]) == -1) {
					hide = true;
					break;
				}
			}

			item.classList.toggle(CSS.displayNone, hide);
		});
	}

	/**
	 * Render the filter input.
	 *
	 * @return the filter input element
	 */
	private render(): InputElement {
		const placeholder = App.text(this.elementAttributes.placeholder);
		const input = create(
			'input',
			[CSS.filterInput, CSS.input],
			{ placeholder },
			this
		);
		const keyComboBadge = create('span', [CSS.filterKeyCombo], {}, this);
		create('am-key-combo-badge', [], { [Attr.key]: 'K' }, keyComboBadge);

		return input;
	}
}

customElements.define('am-filter', FilterComponent);
