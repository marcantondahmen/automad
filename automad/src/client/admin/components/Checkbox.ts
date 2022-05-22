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

import { classes, create, html, listen, query } from '../core';
import { BaseComponent } from './Base';

/**
 * A simple checkbox component.
 *
 * @example
 * <am-checkbox name="..." checked></am-checkbox>
 *
 * @extends BaseComponent
 */
class CheckboxComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['name'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.render();

		this.removeAttribute('name');
		this.removeAttribute('checkbox');

		const toggleParent = () => {
			this.closest(`.${classes.card}`).classList.toggle(
				classes.cardActive,
				(query('input', this) as HTMLInputElement).checked
			);
		};

		listen(this, 'input', toggleParent, 'input');
		toggleParent();
	}

	/**
	 * Render the checkbox.
	 */
	render(): void {
		this.classList.add(classes.checkbox);

		const label = create('label', [], {}, this);

		label.innerHTML = html`
			<input
				type="checkbox"
				name="${this.elementAttributes.name}"
				${this.hasAttribute('checked') ? 'checked' : ''}
				${this.hasAttribute('value')
					? `value="${this.getAttribute('value')}"`
					: ''}
			/>
			<i class="bi"></i>
		`;

		this.removeAttribute('checked');
		this.removeAttribute('value');
	}
}

customElements.define('am-checkbox', CheckboxComponent);
