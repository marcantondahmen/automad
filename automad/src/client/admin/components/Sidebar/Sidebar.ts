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
 * Copyright (c) 2021-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { CSS, debounce } from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';

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
export class SidebarComponent extends BaseComponent {
	/**
	 * The sidebar state.
	 * Note that this static property assumes that there is only one sidebar component
	 * in the document.
	 *
	 * @static
	 */
	static open = false;

	/**
	 * The scoll cache that saves the scroll positions between route changes.
	 *
	 * @static
	 */
	static savedScroll = 0;

	/**
	 * Toggle the sidebar from anywhere.
	 *
	 * @param [state]
	 * @static
	 */
	static toggle(state?: boolean): void {
		const open = state ?? !SidebarComponent.open;

		document.body.classList.toggle(CSS.lauoutDashboardSidebarOpen, open);
		document.body.classList.toggle(CSS.overflowHidden, open);

		SidebarComponent.open = open;
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.setHeight();

		setTimeout(() => {
			this.setHeight();
			this.scrollTo(0, SidebarComponent.savedScroll);

			this.listen(
				window,
				'resize',
				debounce(this.setHeight.bind(this), 200)
			);

			this.listen(this, 'scroll', () => {
				SidebarComponent.savedScroll = this.scrollTop;
			});
		}, 0);
	}

	/**
	 * Set the container height.
	 */
	private setHeight(): void {
		this.style.setProperty('height', `${window.innerHeight}px`);
	}
}

customElements.define('am-sidebar', SidebarComponent);
