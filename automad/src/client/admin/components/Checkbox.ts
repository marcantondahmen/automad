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

import { create, CSS, html, query } from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';

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
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.render();

		this.removeAttribute('name');
		this.removeAttribute('checkbox');

		const toggleParent = () => {
			this.closest(`.${CSS.card}`).classList.toggle(
				CSS.cardActive,
				query<HTMLInputElement>('input', this).checked
			);
		};

		this.listen(this, 'input', toggleParent, 'input');

		toggleParent();
	}

	/**
	 * Render the checkbox.
	 */
	render(): void {
		this.classList.add(CSS.checkbox);

		create(
			'label',
			[],
			{},
			this,
			html`
				<input
					type="checkbox"
					name="${this.getAttribute('name')}"
					${this.hasAttribute('checked') ? 'checked' : ''}
					${this.hasAttribute('value')
						? `value="${this.getAttribute('value')}"`
						: ''}
				/>
				<i class="bi"></i>
			`
		);

		this.removeAttribute('checked');
		this.removeAttribute('name');
		this.removeAttribute('value');
	}
}

customElements.define('am-checkbox', CheckboxComponent);
