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

import { App, Attr, CSS, html, Route } from '@/core';
import { Section } from 'common';
import { BaseUpdateIndicatorComponent } from '@/components/Indicators/BaseUpdateIndicator';

/**
 * A system update state button component.
 *
 * @extends BaseUpdateIndicatorComponent
 */
class NavbarUpdateIndicatorComponent extends BaseUpdateIndicatorComponent {
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
				</am-link>
			`;
		} else {
			this.innerHTML = '';
		}
	}
}

customElements.define(
	'am-navbar-update-indicator',
	NavbarUpdateIndicatorComponent
);
