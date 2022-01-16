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

import { classes, getDashboardURL, isActiveView, text } from '../utils/core';
import { BaseComponent } from './Base';

/**
 * A simple link in the sidebar navigation.
 *
 * @example
 * <am-nav-item view="System" icon="sliders" text="System"></am-nav-item>
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
		return ['view', 'icon', 'text'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(classes.navItem);
		this.classList.toggle(
			classes.navItemActive,
			isActiveView(this.elementAttributes.view)
		);

		this.innerHTML = this.render();
	}

	/**
	 * Render the component.
	 *
	 * @returns the rendered HTML
	 */
	render(): string {
		let link = './';

		if (this.elementAttributes.view) {
			link = `${getDashboardURL()}/${this.elementAttributes.view}`;
		}

		return `
			<a 
			href="${link}" 
			class="${classes.navLink}"
			>
				<i class="bi bi-${this.elementAttributes.icon}"></i>
				<span>${text(this.elementAttributes.text)}</span>
			</a>
		`;
	}
}

customElements.define('am-nav-item', NavItemComponent);
