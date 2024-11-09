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
 * Copyright (c) 2023-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	createField,
	CSS,
	FieldTag,
	InputPattern,
	uniqueId,
} from '@/admin/core';
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
		this.classList.add(CSS.field);

		createField(
			FieldTag.input,
			this,
			{
				id: uniqueId(),
				key: 'username',
				value: this.getAttribute('value') ?? '',
				name: 'username',
				label: App.text('username'),
			},
			[],
			{
				pattern: InputPattern.username,
				required: '',
				[Attr.error]: App.text('invalidUsernameError'),
			}
		);
	}
}

customElements.define('am-user-name', UserNameComponent);
