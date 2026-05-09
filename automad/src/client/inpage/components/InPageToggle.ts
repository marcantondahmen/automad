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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { create, InPageController } from '@/common';
import { inPageRequest } from '../request';
import { saveScrollPosition } from '../sessionStore';
import { BaseInPageComponent } from './BaseInPageComponent';

/**
 * The InPage edit toggle component.
 */
export class InPageToggleComponent extends BaseInPageComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		const api = this.getAttr('api');
		const csrf = this.getAttr('csrf');
		const enabled = parseInt(this.getAttr('editing')) === 1;

		const toggle = async (): Promise<void> => {
			const data = await inPageRequest(
				api,
				InPageController.toggle,
				csrf,
				{
					enabled: !enabled,
				}
			);

			if (data.code != 200) {
				return;
			}

			saveScrollPosition();

			window.location.reload();
		};

		create(
			'i',
			['bi', `bi-toggle-${enabled ? 'on' : 'off'}`],
			{},
			create('span', [], {}, this)
		);

		this.addEventListener('click', toggle.bind(this));
	}
}

customElements.define('am-inpage-toggle', InPageToggleComponent);
