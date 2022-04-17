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

import { App, classes, eventNames, html, listen, Routes } from '../../core';
import { BaseComponent } from '../Base';
import { Sections } from '../Switcher/Switcher';

/**
 * A system update state button component.
 *
 * @extends BaseComponent
 */
class SystemUpdateButtonComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.render();
		this.listeners.push(
			listen(window, eventNames.systemUpdateCheck, this.render.bind(this))
		);
	}

	/**
	 * Render the state element.
	 */
	render(): void {
		this.classList.toggle(
			classes.displayNone,
			!App.state.systemUpdate?.pending
		);

		if (App.state.systemUpdate?.pending) {
			this.innerHTML = html`
				<am-link
					class="${classes.button}"
					target="${Routes.system}?section=${Sections.update}"
				>
					<am-icon-text
						icon="arrow-down-circle"
						text="${App.text('systemUpdate')}"
					></am-icon-text>
				</am-link>
			`;
		} else {
			this.innerHTML = '';
		}
	}
}

customElements.define('am-system-update-button', SystemUpdateButtonComponent);
