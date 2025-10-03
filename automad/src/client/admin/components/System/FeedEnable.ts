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
 * A wrapper element for initializing the feed fields enable checkbox.
 *
 * @extends BaseComponent
 */
class FeedEnableComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const feedEnabled = new Binding('feedEnabled', {
			initial: App.system.feed.enabled,
		});

		this.listen(window, EventName.appStateChange, () => {
			feedEnabled.value = App.system.feed.enabled;
		});

		createField(
			FieldTag.toggleLarge,
			this,
			{
				key: 'feedEnabled',
				value: App.system.feed.enabled,
				name: 'feedEnabled',
				label: App.text('systemRssFeedEnable'),
			},
			[],
			{
				[Attr.bind]: 'feedEnabled',
				[Attr.bindTo]: 'checked',
				[Attr.toggle]: '#am-feed-settings',
			}
		);
	}
}

customElements.define('am-feed-enable', FeedEnableComponent);
