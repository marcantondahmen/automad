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

		createSelect(
			[
				{ value: 60, text: '1 min' },
				{ value: 120, text: '2 min' },
				{ value: 300, text: '5 min' },
				{ value: 600, text: '10 min' },
			],
			`${App.system.cache.monitorDelay}`,
			this,
			'cacheMonitorDelay',
			'',
			App.text('systemCacheMonitor'),
			[CSS.selectInline],
			{
				[Attr.bind]: 'cacheMonitorDelay',
				[Attr.bindTo]: 'value',
			}
		);
	}
}

customElements.define('am-cache-monitor', CacheMonitorComponent);
