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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { applyTheme, CSS, getTheme, html } from '@/admin/core';
import { BaseComponent } from './Base';

/**
 * The login view.
 *
 * @extends BaseComponent
 */
class MaintenanceComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		applyTheme(getTheme());

		const baseIndex = this.getAttribute('base-index');

		this.setAttribute('hidden', '');

		setTimeout(() => {
			const message = this.innerHTML;

			this.innerHTML = html`
				<div class="${CSS.layoutCentered}">
					<div class="${CSS.layoutCenteredNavbar}">
						<nav class="${CSS.navbar}">
							<a
								href="${baseIndex || '/'}"
								class="${CSS.navbarItem}"
							>
								<am-logo></am-logo>
							</a>
							<a
								href="${baseIndex || '/'}"
								class="${CSS.navbarItem}"
							>
								<i class="bi bi-x"></i>
							</a>
						</nav>
					</div>
					<div class="${CSS.layoutCenteredMain}">
						<div class="${CSS.layoutCenteredContent}">
							<div class="${CSS.card}">
								<div class="${CSS.cardIcon}">
									<i class="bi bi-cup-hot-fill"></i>
								</div>
								<div
									class="${CSS.cardBody} ${CSS.cardBodyLarge}"
								>
									${message}
								</div>
							</div>
						</div>
					</div>
				</div>
			`;

			this.removeAttribute('hidden');
		}, 0);

		setInterval(() => {
			window.location.reload();
		}, 10000);
	}
}

customElements.define('am-maintenance', MaintenanceComponent);
