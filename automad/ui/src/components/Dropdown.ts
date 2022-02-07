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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { classes, listen } from '../core';
import { BaseComponent } from './Base';

/**
 * A simple dropdown menu component.
 *
 * @example
 * <am-dropdown>
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
		listen(window, 'click', (event: MouseEvent) => {
			if (event.target === this) {
				this.classList.toggle(classes.dropdownOpen);
			} else {
				this.classList.remove(classes.dropdownOpen);
			}
		});
	}
}

customElements.define('am-dropdown', DropdownComponent);
