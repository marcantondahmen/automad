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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, CSS, html, Routes } from '../../../core';
import { BaseUpdateIndicatorComponent } from '../BaseUpdateIndicator';

/**
 * A packages state component.
 *
 * @extends BaseUpdateIndicatorComponent
 */
class NavbarOutdatedPackagesIndicatorComponent extends BaseUpdateIndicatorComponent {
	/**
	 * Render the state element.
	 */
	render(): void {
		const count = App.state.outdatedPackages;

		this.classList.toggle(CSS.displayNone, !count);

		if (App.state.systemUpdate?.pending) {
			this.innerHTML = html`
				<am-link
					class="${CSS.navbarItem}"
					target="${Routes.packages}"
					am-tooltip="${App.text('packagesUpdatesAvailable')}"
				>
					<i class="bi bi-box-seam"></i>
					<span class="am-e-badge"></span>
				</am-link>
			`;
		} else {
			this.innerHTML = '';
		}
	}
}

customElements.define(
	'am-navbar-outdated-packages-indicator',
	NavbarOutdatedPackagesIndicatorComponent
);
