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

import { App } from '../core/app';
import { listen } from '../core/utils';
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
		listen(this, 'click', () => {
			if (this.elementAttributes.external) {
				window.location = this.elementAttributes.external;
			}

			const base = `${window.location.origin}${App.dashboardURL}/`;
			const url = new URL(this.elementAttributes.target, base);

			window.history.pushState(null, null, url);
			App.root.updateView();
		});
	}
}

customElements.define('am-link', LinkComponent);
