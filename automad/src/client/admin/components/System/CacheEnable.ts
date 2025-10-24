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

import {
	App,
	Attr,
	Binding,
	createField,
	EventName,
	FieldTag,
} from '@/admin/core';
import { BaseComponent } from '../Base';

/**
 * A wrapper element for initializing the cache enable checkbox.
 *
 * @extends BaseComponent
 */
class CacheEnableComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const cacheEnabled = new Binding('cacheEnabled', {
			initial: App.system.cache.enabled,
		});

		this.listen(window, EventName.appStateChange, () => {
			cacheEnabled.value = App.system.cache.enabled;
		});

		createField(
			FieldTag.toggleLarge,
			this,
			{
				key: 'cacheEnabled',
				value: App.system.cache.enabled,
				name: 'cacheEnabled',
				label: App.text('systemCacheEnable'),
			},
			[],
			{
				[Attr.toggle]: '.am-cache-settings',
				[Attr.bind]: 'cacheEnabled',
				[Attr.bindTo]: 'checked',
			}
		);
	}
}

customElements.define('am-cache-enable', CacheEnableComponent);
