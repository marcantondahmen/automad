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

import { CSS, EventName, listen, query } from '../core';
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
class SelectComponent extends BaseComponent {
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
		const select = query('select', this) as HTMLSelectElement;
		const update = () => {
			span.textContent = select.options[select.selectedIndex].text;
		};

		update();

		listen(select, `change ${EventName.changeByBinding}`, update);
	}
}

customElements.define('am-select', SelectComponent);
