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
 * A wrapper element for initializing the cache monitor delay select.
 *
 * @extends BaseComponent
 */
class CacheMonitorComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const cacheMonitorDelay = new Binding('cacheMonitorDelay', {
			initial: App.system.cache.monitorDelay,
		});

		this.listen(window, EventName.appStateChange, () => {
			cacheMonitorDelay.value = App.system.cache.monitorDelay;
		});

		const options = [60, 120, 300, 600];

		if (!options.includes(App.system.cache.monitorDelay)) {
			options.push(App.system.cache.monitorDelay);
		}

		createSelect(
			options.map((o) => ({
				value: o,
				text: `${Math.round(o / 60)} min`,
			})),
			`${App.system.cache.monitorDelay}`,
			this,
			'cacheMonitorDelay',
			'',
			App.text('systemCacheMonitor'),
			[CSS.selectInline],
			{
				[Attr.bind]: 'cacheMonitorDelay',
				[Attr.bindTo]: 'value',
			},
			'AM_CACHE_MONITOR_DELAY'
		);
	}
}

customElements.define('am-cache-monitor', CacheMonitorComponent);
