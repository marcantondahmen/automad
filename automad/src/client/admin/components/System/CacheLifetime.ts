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
 * Copyright (c) 2023-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
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

		const options = [3600, 21600, 43200, 86400];

		if (!options.includes(App.system.cache.lifetime)) {
			options.push(App.system.cache.lifetime);
		}

		createSelect(
			options.map((o) => ({
				value: o,
				text: `${Math.round(o / 60 / 60)} h`,
			})),
			`${App.system.cache.lifetime}`,
			this,
			'cacheLifetime',
			'',
			App.text('systemCacheLifetime'),
			[CSS.selectInline],
			{
				[Attr.bind]: 'cacheLifetime',
				[Attr.bindTo]: 'value',
			},
			'AM_CACHE_LIFETIME'
		);
	}
}

customElements.define('am-cache-lifetime', CacheLifetimeComponent);
