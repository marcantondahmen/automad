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
 * A wrapper element for initializing the user email input field.
 *
 * @extends BaseComponent
 */
class UserEmailComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const email = new Binding('email', { initial: App.user.email });

		this.addListener(
			listen(window, EventName.appStateChange, () => {
				email.value = App.user.email;
			})
		);

		createField(
			'am-email',
			this,
			{
				key: 'email',
				value: App.user.email,
				name: 'email',
				label: App.text('email'),
			},
			[],
			{ [Attr.bind]: 'email', [Attr.bindTo]: 'value' }
		);
	}
}

customElements.define('am-user-email', UserEmailComponent);
