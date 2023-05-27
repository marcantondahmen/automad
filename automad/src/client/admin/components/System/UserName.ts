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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, Binding, createField, EventName, listen } from '@/core';
import { BaseComponent } from '../Base';

/**
 * A wrapper element for initializing the user name input field.
 *
 * @extends BaseComponent
 */
class UserNameComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const username = new Binding('username', { initial: App.user.name });

		this.addListener(
			listen(window, EventName.appStateChange, () => {
				username.value = App.user.name;
			})
		);

		createField(
			'am-input',
			this,
			{
				key: 'username',
				value: App.user.name,
				name: 'username',
				label: App.text('username'),
			},
			[],
			{
				[Attr.bind]: 'username',
				[Attr.bindTo]: 'value',
			}
		);
	}
}

customElements.define('am-user-name', UserNameComponent);
