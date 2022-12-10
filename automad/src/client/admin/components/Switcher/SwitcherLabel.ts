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
import { Attr, EventName, listen, query } from '../../core';
import { getActiveSection, SwitcherComponent } from './Switcher';
import { linkTag } from './SwitcherLink';

/**
 * A label that reflects the active switcher link content.
 *
 * @see {@link SwitcherComponent}
 * @extends BaseComponent
 */

export class SwitcherLabelComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.listeners.push(
			listen(window, EventName.switcherChange, () => {
				this.innerHTML = query(
					`${linkTag}[${Attr.section}="${getActiveSection()}"]`
				).innerHTML;
			})
		);
	}
}

customElements.define('am-switcher-label', SwitcherLabelComponent);
