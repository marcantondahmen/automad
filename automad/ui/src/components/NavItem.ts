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
import { classes, isActiveView } from '../core/utils';
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
		return `
			<am-link 
			target="${this.elementAttributes.view}" 
			class="${classes.navLink}"
			>
				<i class="bi bi-${this.elementAttributes.icon}"></i>
				<span>${App.text(this.elementAttributes.text)}</span>
			</am-link>
		`;
	}
}

customElements.define('am-nav-item', NavItemComponent);
