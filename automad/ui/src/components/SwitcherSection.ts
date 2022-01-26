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

import { BaseComponent } from './Base';
import { classes, listen, query } from '../core';
import {
	getActiveSection,
	switcherChangeEventName,
	SwitcherComponent,
} from './Switcher';

/**
 * A switcher section that contains the content that will be toggled by a switcher menu.
 *
 * @example
 * <am-switcher-section name="settings">...</am-switcher-section>
 * <am-switcher-section name="text">...</am-switcher-section>
 *
 * @see {@link SwitcherComponent}
 * @extends BaseComponent
 */
export class SwitcherSectionComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['name'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.toggle();

		listen(window, switcherChangeEventName, this.toggle.bind(this));
	}

	/**
	 * Toggle the section visiblity based on the query string.
	 */
	toggle(): void {
		this.classList.toggle(
			classes.displayNone,
			this.elementAttributes.name != getActiveSection()
		);

		query('html').scrollTop = 0;
	}
}

customElements.define('am-switcher-section', SwitcherSectionComponent);
