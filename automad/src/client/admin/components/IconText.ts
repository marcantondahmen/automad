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

import { Attr, create, CSS } from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';

/**
 * A simple icon with text component.
 *
 * @example
 * <am-icon-text ${Attr.icon}="..." ${Attr.text}="..."></am-icon-text>
 *
 * @extends BaseComponent
 */
class IconTextComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const icon = this.getAttribute(Attr.icon);
		const text = this.getAttribute(Attr.text);

		this.removeAttribute(Attr.icon);
		this.removeAttribute(Attr.text);

		if (icon && text) {
			this.classList.add(CSS.iconText);
			create('i', ['bi', `bi-${icon}`], {}, this);
			create('span', [], {}, this).textContent = text;
		}
	}
}

customElements.define('am-icon-text', IconTextComponent);
