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

import { listen, App } from '../core';
import { BaseComponent } from './Base';

/**
 * A simple link component to change the dashboard view.
 *
 * @example
 * <am-link target="Page?url=..."></am-link>
 * <am-link external="http://..."></am-link>
 *
 * @extends BaseComponent
 */
class LinkComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['target', 'external'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		listen(this, 'click', (event: Event) => {
			if (this.elementAttributes.external) {
				window.location.href = this.elementAttributes.external;
			}

			if (App.navigationIsLocked) {
				return;
			}

			const base = `${window.location.origin}${App.dashboardURL}/`;
			const url = new URL(this.elementAttributes.target, base);

			event.stopImmediatePropagation();
			App.root.setView(url);
		});
	}
}

customElements.define('am-link', LinkComponent);
