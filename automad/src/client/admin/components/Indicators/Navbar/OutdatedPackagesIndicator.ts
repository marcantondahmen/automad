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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, CSS, html, Route, Section } from '@/admin/core';
import { BaseOutdatedPackagesIndicator } from '../BaseOutdatedPackagesIndicator';

/**
 * A packages state component.
 *
 * @extends BaseOutdatedPackagesIndicator
 */
class NavbarOutdatedPackagesIndicatorComponent extends BaseOutdatedPackagesIndicator {
	/**
	 * Render the state element.
	 */
	render(): void {
		const count = App.state.outdatedPackages;

		this.classList.toggle(CSS.displayNone, !count);

		if (count > 0) {
			this.innerHTML = html`
				<am-link
					class="${CSS.navbarItem}"
					${Attr.target}="${Route.packages}?section=${Section.packages}"
					${Attr.tooltip}="${App.text('packagesUpdatesAvailable')}"
				>
					<i class="bi bi-box-seam"></i>
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
