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

/**
 * The sidebar scroll container.
 * ```
 * <am-sidebar class="am-l-sidebar__content">
 *     <div class="am-l-sidebar__logo">...</div>
 *     <div class="am-l-sidebar__nav">
 *         <nav class="am-c-nav">
 *             <span class="am-c-nav__item">
 *                 <a href="" class="am-c-nav__link">Home</a>
 *             </span>
 *             <am-nav-item view="" icon="window-sidebar" text="Dashboard"></am-nav-item>
 *             ...
 *         </nav>
 *         <nav class="am-c-nav">
 *             <span class="am-c-nav__label">Pages</span>
 *             <am-nav-tree pages='[{ ... }]'></am-nav-tree>
 *         </nav>
 *     </div>
 * </am-sidebar>
 * ```
 *
 * @extends BaseComponent
 */
class Sidebar extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		setTimeout(this.setHeightAndScroll.bind(this), 0);

		listen(
			window,
			'resize',
			debounce(this.setHeightAndScroll.bind(this), 200)
		);
	}

	/**
	 * Set the container height and scroll to the active item.
	 */
	setHeightAndScroll() {
		const activeItem = query(`.${this.cls.navItemActive}`, this);

		this.style.setProperty('height', `${window.innerHeight}px`);

		if (activeItem) {
			activeItem.scrollIntoView();
		}
	}
}

customElements.define('am-sidebar', Sidebar);
