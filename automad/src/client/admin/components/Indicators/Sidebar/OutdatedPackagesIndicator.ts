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

import { App, CSS, html } from '../../../core';
import { BaseUpdateIndicatorComponent } from '../BaseUpdateIndicator';

/**
 * A packages state component.
 *
 * @extends BaseUpdateIndicatorComponent
 */
class SidebarOutdatedPackagesIndicatorComponent extends BaseUpdateIndicatorComponent {
	/**
	 * Render the state element.
	 */
	render(): void {
		const count = App.state.outdatedPackages;

		this.classList.toggle(CSS.badge, count > 0);

		if (count) {
			this.innerHTML = html`↓ ${count}`;
		} else {
			this.innerHTML = '';
		}
	}
}

customElements.define(
	'am-sidebar-outdated-packages-indicator',
	SidebarOutdatedPackagesIndicatorComponent
);
