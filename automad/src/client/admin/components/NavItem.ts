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

import { isActivePage, App, html, CSS } from '../core';
import { BaseComponent } from './Base';

/**
 * A simple link in the sidebar navigation.
 *
 * @example
 * <am-nav-item page="system" icon="sliders" text="System"></am-nav-item>
 *
 * @extends BaseComponent
 */
class NavItemComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['page', 'icon', 'text', 'badge'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.navItem);
		this.classList.toggle(
			CSS.navItemActive,
			isActivePage(this.elementAttributes.page)
		);

		this.innerHTML = this.render();

		NavItemComponent.observedAttributes.forEach((item) => {
			this.removeAttribute(item);
		});
	}

	/**
	 * Render the component.
	 *
	 * @returns the rendered HTML
	 */
	render(): string {
		const { page, icon, text, badge } = this.elementAttributes;

		return html`
			<am-link target="${page}" class="${CSS.navLink}">
				<span class="${CSS.iconText}">
					<i class="bi bi-${icon}"></i>
					<span>${App.text(text)}</span>
					${badge ? `<${badge}></${badge}>` : ''}
				</span>
			</am-link>
		`;
	}
}

customElements.define('am-nav-item', NavItemComponent);
