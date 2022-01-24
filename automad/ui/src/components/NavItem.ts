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

import { App } from '../core/app';
import { classes, isActivePage } from '../core/utils';
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
		return ['page', 'icon', 'text'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(classes.navItem);
		this.classList.toggle(
			classes.navItemActive,
			isActivePage(this.elementAttributes.page)
		);

		this.innerHTML = this.render();
	}

	/**
	 * Render the component.
	 *
	 * @returns the rendered HTML
	 */
	render(): string {
		return `
			<am-link 
			target="${this.elementAttributes.page}" 
			class="${classes.navLink}"
			>
				<i class="bi bi-${this.elementAttributes.icon}"></i>
				<span>${App.text(this.elementAttributes.text)}</span>
			</am-link>
		`;
	}
}

customElements.define('am-nav-item', NavItemComponent);
