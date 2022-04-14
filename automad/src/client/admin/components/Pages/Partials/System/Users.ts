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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Binding,
	classes,
	createField,
	eventNames,
	html,
	listen,
} from '../../../../core';
import { Listener } from '../../../../types';

/**
 * Create bindings for the form elements in the feed section.
 *
 * @param listeners
 */
const createBindings = (listeners: Listener[]): void => {
	const username = new Binding('username', null, null, App.user.name);
	const email = new Binding('email', null, null, App.user.email);
	const userSubmit = new Binding('userSubmitButton', null, null, true);

	listeners.push(
		listen(window, eventNames.appStateChange, () => {
			username.value = App.user.name;
			email.value = App.user.email;
			userSubmit.value = true;
		})
	);
};

export const renderUsersSection = (listeners: Listener[]): string => {
	createBindings(listeners);

	return html`
		<am-form
			api="User/edit"
			event="${eventNames.appStateRequireUpdate}"
			watch
		>
			<p>$${App.text('systemUsersInfo')}</p>
			${createField(
				'am-input',
				null,
				{
					key: 'username',
					value: App.user.name,
					name: 'username',
					label: App.text('username'),
				},
				[],
				{ bind: 'username', bindto: 'value' }
			).outerHTML}
			${createField(
				'am-email',
				null,
				{
					key: 'email',
					value: App.user.email,
					name: 'email',
					label: App.text('email'),
				},
				[],
				{ bind: 'email', bindto: 'value' }
			).outerHTML}
			<am-submit
				class="${classes.button}"
				bind="userSubmitButton"
				bindto="disabled"
			>
				<am-icon-text
					icon="check"
					text="${App.text('save')}"
				></am-icon-text>
			</am-submit>
		</am-form>
		<p>$${App.text('systemUsersAdd')}</p>
		<p>$${App.text('systemUsersChangePassword')}</p>
		<p>$${App.text('systemUsersInvite')}</p>
		<p>$${App.text('systemUsersRegistered')}</p>
		<p>$${App.text('systemUsersRegisteredInfo')}</p>
		<p>$${App.text('systemUsersYou')}</p>
		<p>$${App.text('newPassword')}</p>
		<p>$${App.text('currentPassword')}</p>
	`;
};
