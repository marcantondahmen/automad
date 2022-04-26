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

import { App, classes, eventNames, html, listen } from '../../core';
import { BaseComponent } from '../Base';

/**
 * A system update state component.
 *
 * @extends BaseComponent
 */
class SystemUpdateIndicatorComponent extends BaseComponent {
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
		if (App.state.systemUpdate?.pending) {
			this.innerHTML = html`
				<span class="${classes.textSuccess} ${classes.iconText}">
					<i class="bi bi-arrow-down-circle-fill"></i>
					<span>
						${App.text('systemUpdateTo')}
						<strong>${App.state.systemUpdate?.latest}</strong>
					</span>
				</span>
			`;

			return;
		}

		this.innerHTML = html`
			<am-icon-text
				class="${classes.textMuted}"
				icon="check-circle-fill"
				text="${App.text('systemUpToDate')}"
			></am-icon-text>
		`;
	}
}

customElements.define(
	'am-system-update-indicator',
	SystemUpdateIndicatorComponent
);
