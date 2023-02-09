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
 * Copyright (c) 2021-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { debounce, listen } from '../../core';
import { BaseComponent } from '../Base';

/**
 * The sidebar scroll container.
 *
 * @example
 * <am-sidebar class="am-l-sidebar__content">
 *     <div class="am-l-sidebar__logo">...</div>
 *     <div class="am-l-sidebar__nav">
 *         <nav class="am-c-nav">
 *             <span class="am-c-nav__item">
 *                 <a href="" class="am-c-nav__link">Home</a>
 *             </span>
 *             <am-nav-item view="" ${Attr.icon}="window-sidebar" ${Attr.text}="Dashboard"></am-nav-item>
 *             ...
 *         </nav>
 *         <nav class="am-c-nav">
 *             <span class="am-c-nav__label">Pages</span>
 *             <am-nav-tree></am-nav-tree>
 *         </nav>
 *     </div>
 * </am-sidebar>
 *
 * @extends BaseComponent
 */
class SidebarComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.setHeight();
		setTimeout(this.setHeight.bind(this), 0);

		this.listeners.push(
			listen(window, 'resize', debounce(this.setHeight.bind(this), 200))
		);
	}

	/**
	 * Set the container height.
	 */
	private setHeight(): void {
		this.style.setProperty('height', `${window.innerHeight}px`);
	}
}

customElements.define('am-sidebar', SidebarComponent);
