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

import { BaseComponent } from './BaseComponent';
import { listen, queryAll } from '../utils/core';

/**
 * A simple toggle link component.
 *
 * ```
 * <am-toggle target="body" cls="am-l-page--sidebar-open">
 *     Menu
 * </am-toggle>
 * ```
 *
 * @extends BaseComponent
 */
class Toggle extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @type {Array}
	 * @static
	 */
	static get observedAttributes() {
		return ['target', 'cls'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		listen(this, 'click', () => {
			const elements = queryAll(this.elementAttributes.target);

			elements.forEach((element) => {
				element.classList.toggle(this.elementAttributes.cls);
			});
		});
	}
}

customElements.define('am-toggle', Toggle);
