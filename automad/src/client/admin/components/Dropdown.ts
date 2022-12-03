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

import { CSS, listen, queryParents } from '../core';
import { BaseComponent } from './Base';

/**
 * A simple dropdown menu component.
 *
 * @example
 * <am-dropdown right>
 *     Menu
 *     <div class="am-c-dropdown__items">
 *         ...
 *     </div>
 * </am-dropdown>
 *
 * @extends BaseComponent
 */
class DropdownComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.dropdown);
		this.classList.toggle(CSS.dropdownRight, this.hasAttribute('right'));

		this.listeners.push(
			listen(window, 'click', (event: MouseEvent) => {
				if (
					event.target === this ||
					queryParents(
						'am-dropdown',
						event.target as HTMLElement
					).includes(this)
				) {
					this.classList.toggle(CSS.dropdownOpen);
				} else {
					this.classList.remove(CSS.dropdownOpen);
				}
			})
		);
	}
}

customElements.define('am-dropdown', DropdownComponent);
