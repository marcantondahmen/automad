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
		return ['name', 'checked'];
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
		const label = create('label', [classes.checkbox], {}, this);

		label.innerHTML = html`
			<input
				type="checkbox"
				name="${this.elementAttributes.name}"
				${this.hasAttribute('checked') ? 'checked' : ''}
			/>
			<i class="bi"></i>
		`;
	}
}

customElements.define('am-checkbox', CheckboxComponent);
