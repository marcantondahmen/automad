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

import { App, Attr, CSS, html, Route } from '../../../core';
import { Section } from '../../Switcher/Switcher';
import { BaseUpdateIndicatorComponent } from '../BaseUpdateIndicator';

/**
 * A system update state button component.
 *
 * @extends BaseUpdateIndicatorComponent
 */
class NavbarSystemUpdateIndicatorComponent extends BaseUpdateIndicatorComponent {
	/**
	 * Render the state element.
	 */
	render(): void {
		this.classList.toggle(
			CSS.displayNone,
			!App.state.systemUpdate?.pending
		);

		if (App.state.systemUpdate?.pending) {
			this.innerHTML = html`
				<am-link
					class="${CSS.navbarItem}"
					${Attr.target}="${Route.system}?section=${Section.update}"
					${Attr.tooltip}="${App.text('systemUpdateTooltip')}"
				>
					<i class="bi bi-download"></i>
					<span class="am-e-badge"></span>
				</am-link>
			`;
		} else {
			this.innerHTML = '';
		}
	}
}

customElements.define(
	'am-navbar-system-update-indicator',
	NavbarSystemUpdateIndicatorComponent
);
