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

import { BaseComponent } from '../Base';
import { App, CSS, eventNames, listen } from '../../core';
import {
	getActiveSection,
	setActiveSection,
	SwitcherComponent,
} from './Switcher';

export const linkTag = 'am-switcher-link';

/**
 * A switcher link that is part of a switcher menu component.
 *
 * @see {@link SwitcherComponent}
 * @extends BaseComponent
 */
export class SwitcherLinkComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['section'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.listeners.push(
			listen(window, eventNames.switcherChange, this.toggle.bind(this))
		);

		this.listeners.push(listen(this, 'click', this.select.bind(this)));
	}

	/**
	 * Toggle the active state of the switcher link.
	 */
	toggle(): void {
		this.classList.toggle(
			CSS.menuItemActive,
			this.elementAttributes.section == getActiveSection()
		);
	}

	/**
	 * Set the active section.
	 */
	select(): void {
		if (App.navigationIsLocked) {
			return;
		}

		setActiveSection(this.elementAttributes.section);
	}
}

customElements.define(linkTag, SwitcherLinkComponent);
