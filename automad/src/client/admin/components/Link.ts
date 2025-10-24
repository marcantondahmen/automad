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

import { App, Attr } from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';

/**
 * A simple link component to change the dashboard view.
 *
 * @example
 * <am-link ${Attr.target}="page?url=..."></am-link>
 * <am-link ${Attr.external}="http://..."></am-link>
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
		return [Attr.target, Attr.external];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.listen(this, 'click', (event: Event) => {
			if (this.elementAttributes[Attr.external]) {
				window.location.href = this.elementAttributes[Attr.external];

				return;
			}

			if (App.navigationIsLocked) {
				return;
			}

			const base = `${window.location.origin}${App.dashboardURL}/`;
			const url = new URL(this.elementAttributes[Attr.target], base);

			event.stopImmediatePropagation();
			App.root.setView(url);
		});
	}
}

customElements.define('am-link', LinkComponent);
