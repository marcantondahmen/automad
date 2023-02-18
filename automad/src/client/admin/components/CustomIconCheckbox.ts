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

import { Attr, create, CSS, html, query } from '../core';
import { BaseComponent } from './Base';

/**
 * A custom icon checkbox field.
 *
 * @example
 * <am-custom-icon-checkbox
 *     ${Attr.icon}="icon"
 *     name="name"
 *     checked
 * ></am-custom-icon-checkbox>
 *
 * @extends BaseComponent
 */
export class CustomIconCheckboxComponent extends BaseComponent {
	/**
	 * The actual checkbox state.
	 */
	get checked(): boolean {
		const input = query('input', this) as HTMLInputElement;

		return input.checked;
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.customIconCheckbox);

		create(
			'label',
			[],
			{},
			this,
			html`
				<input
					type="checkbox"
					name="${this.getAttribute('name')}"
					value="1"
					${this.hasAttribute('checked') ? 'checked' : ''}
				/>
				<span>
					<i class="bi bi-${this.getAttribute(Attr.icon)}"></i>
				</span>
			`
		);

		this.removeAttribute(Attr.icon);
		this.removeAttribute('name');
		this.removeAttribute('checked');
	}
}

customElements.define('am-custom-icon-checkbox', CustomIconCheckboxComponent);
