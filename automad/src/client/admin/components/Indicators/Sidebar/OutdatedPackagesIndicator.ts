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
 * Copyright (c) 2021-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, CSS, html } from '@/admin/core';
import { BaseOutdatedPackagesIndicator } from '../BaseOutdatedPackagesIndicator';

/**
 * A packages state component.
 *
 * @extends BaseOutdatedPackagesIndicator
 */
class SidebarOutdatedPackagesIndicatorComponent extends BaseOutdatedPackagesIndicator {
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
