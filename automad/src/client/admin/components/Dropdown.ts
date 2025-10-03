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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { Attr, CSS, queryParents } from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';

/**
 * A simple dropdown menu component.
 *
 * @example
 * <am-dropdown ${Attr.right}>
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
	 * The tag name.
	 *
	 * @static
	 * @readonly
	 */
	static readonly TAG_NAME = 'am-dropdown';

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.dropdown);
		this.classList.toggle(CSS.dropdownRight, this.hasAttribute(Attr.right));

		this.listen(window, 'click', (event: MouseEvent) => {
			if (
				event.target === this ||
				queryParents(
					DropdownComponent.TAG_NAME,
					event.target as HTMLElement
				).includes(this)
			) {
				this.classList.toggle(CSS.dropdownOpen);
			} else {
				this.classList.remove(CSS.dropdownOpen);
			}
		});
	}
}

customElements.define(DropdownComponent.TAG_NAME, DropdownComponent);
