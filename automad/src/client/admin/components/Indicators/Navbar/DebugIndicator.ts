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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, CSS, html, Route } from '@/admin/core';
import { Section } from '@/common';
import { BaseStateIndicatorComponent } from '@/admin/components/Indicators/BaseStateIndicator';

/**
 * A debug state component.
 *
 * @extends BaseStateIndicatorComponent
 */
class NavbarDebugIndicatorComponent extends BaseStateIndicatorComponent {
	/**
	 * Render the state element.
	 */
	render(): void {
		this.classList.toggle(CSS.displayNone, !App.system.debug.enabled);

		if (App.system.debug.enabled) {
			this.innerHTML = html`
				<am-link
					class="${CSS.navbarItem}"
					${Attr.target}="${Route.system}?section=${Section.debug}"
					${Attr.tooltip}="${App.text('debugEnabled')}"
				>
					<i class="bi bi-bug"></i>
				</am-link>
			`;
		} else {
			this.innerHTML = '';
		}
	}
}

customElements.define(
	'am-navbar-debug-indicator',
	NavbarDebugIndicatorComponent
);
