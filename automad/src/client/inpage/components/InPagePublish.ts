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
 * Copyright (c) 2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { InPageController } from 'common';
import { inPageRequest } from '../request';
import { BaseInPageComponent } from './BaseInPageComponent';

/**
 * The InPage publishing component.
 */
export class InPagePublishComponent extends BaseInPageComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		const state = this.getAttr('state');
		const url = this.getAttr('url');
		const api = this.getAttr('api');
		const csrf = this.getAttr('csrf');
		const label = this.getAttr('label');

		this.textContent = label;

		if (state === 'draft') {
			this.addEventListener('click', async () => {
				const data = await inPageRequest(
					api,
					InPageController.publish,
					csrf,
					{
						url,
					}
				);

				if (data.redirect) {
					window.location.href = `${data.redirect}`;

					return;
				}

				window.location.reload();
			});
		} else {
			this.setAttribute('disabled', '');
		}
	}
}

customElements.define('am-inpage-publish', InPagePublishComponent);
