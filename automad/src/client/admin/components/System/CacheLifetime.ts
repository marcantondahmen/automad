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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, Binding, createSelect, CSS, EventName } from '@/admin/core';
import { BaseComponent } from '../Base';

/**
 * A wrapper element for initializing the cache liftime select.
 *
 * @extends BaseComponent
 */
class CacheLifetimeComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const cacheLifetime = new Binding('cacheLifetime', {
			initial: App.system.cache.lifetime,
		});

		this.listen(window, EventName.appStateChange, () => {
			cacheLifetime.value = App.system.cache.lifetime;
		});

		createSelect(
			[
				{ value: 3600, text: '1 h' },
				{ value: 21600, text: '6 h' },
				{ value: 43200, text: '12 h' },
				{ value: 86400, text: '24 h' },
			],
			`${App.system.cache.lifetime}`,
			this,
			'cacheLifetime',
			'',
			App.text('systemCacheLifetime'),
			[CSS.selectInline],
			{
				[Attr.bind]: 'cacheLifetime',
				[Attr.bindTo]: 'value',
			}
		);
	}
}

customElements.define('am-cache-lifetime', CacheLifetimeComponent);
