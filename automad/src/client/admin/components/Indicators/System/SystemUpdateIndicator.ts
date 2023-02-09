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
 * Copyright (c) 2021-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, CSS, EventName, html, listen } from '../../../core';
import { BaseUpdateIndicatorComponent } from '../BaseUpdateIndicator';

/**
 * A system update state component.
 *
 * @extends BaseUpdateIndicatorComponent
 */
class SystemUpdateIndicatorComponent extends BaseUpdateIndicatorComponent {
	/**
	 * Render the state element.
	 */
	render(): void {
		if (App.state.systemUpdate?.pending) {
			this.innerHTML = html`
				<span class="${CSS.textActive} ${CSS.iconText}">
					<i class="bi bi-download"></i>
					<span>
						${App.text('systemUpdateTo')}
						${App.state.systemUpdate?.latest}
					</span>
				</span>
			`;

			return;
		}

		this.innerHTML = html`
			<am-icon-text
				class="${CSS.textMuted}"
				${Attr.icon}="check-circle"
				${Attr.text}="${App.text('systemUpToDate')}"
			></am-icon-text>
		`;
	}
}

customElements.define(
	'am-system-update-indicator',
	SystemUpdateIndicatorComponent
);
