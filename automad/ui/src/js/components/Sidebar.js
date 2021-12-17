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

import { debounce, listen, query } from '../utils/core';
import { BaseComponent } from './BaseComponent';

class Sidebar extends BaseComponent {
	connectedCallback() {
		setTimeout(this.setHeightAndScroll.bind(this), 0);

		listen(
			window,
			'resize',
			debounce(this.setHeightAndScroll.bind(this), 200)
		);
	}

	setHeightAndScroll() {
		const activeItem = query(`.${this.cls.navItemActive}`, this);

		this.style.setProperty('height', `${window.innerHeight}px`);

		if (activeItem) {
			activeItem.scrollIntoView();
		}
	}
}

customElements.define('am-sidebar', Sidebar);
