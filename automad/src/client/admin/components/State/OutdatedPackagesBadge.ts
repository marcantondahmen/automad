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

import { classes, eventNames, html, listen, requestAPI } from '../../core';
import { BaseComponent } from '../Base';

/**
 * A packages state component.
 *
 * @extends BaseComponent
 */
class OutdatedPackagesBadgeComponent extends BaseComponent {
	static count = 0;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.render();
		this.init();

		this.listeners.push(
			listen(window, eventNames.packagesChange, this.init.bind(this))
		);
	}

	private async init(): Promise<void> {
		const { data } = await requestAPI('PackageManager/getOutdated');

		OutdatedPackagesBadgeComponent.count = data?.outdated?.length || 0;
		this.render();
	}

	/**
	 * Render the state element.
	 */
	render(): void {
		const count = OutdatedPackagesBadgeComponent.count;

		this.classList.toggle(classes.badge, count > 0);

		if (count) {
			this.innerHTML = html`
				<span
					class="${classes.flex} ${classes.flexGap} ${classes.flexAlignCenter}"
				>
					<i class="bi bi-arrow-down-circle"></i>
					<span>${count}</span>
				</span>
			`;
		} else {
			this.innerHTML = '';
		}
	}
}

customElements.define(
	'am-outdated-packages-badge',
	OutdatedPackagesBadgeComponent
);
