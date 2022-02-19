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

import { BaseComponent } from '../Base';
import { classes, listen } from '../../core';
import {
	getActiveSection,
	setActiveSection,
	switcherChangeEventName,
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
		listen(window, switcherChangeEventName, this.toggle.bind(this));
		listen(this, 'click', this.select.bind(this));
	}

	/**
	 * Toggle the active state of the switcher link.
	 */
	toggle(): void {
		this.classList.toggle(
			classes.switcherLinkActive,
			this.elementAttributes.section == getActiveSection()
		);
	}

	/**
	 * Set the active section.
	 */
	select(): void {
		setActiveSection(this.elementAttributes.section);
	}
}

customElements.define('am-switcher-link', SwitcherLinkComponent);
