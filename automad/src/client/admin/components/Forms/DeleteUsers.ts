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

import { App, classes, eventNames, html, listen } from '../../core';
import { FormComponent } from './Form';

/**
 * Render the list of registered users.
 *
 * @returns the rendered list
 */
const renderRegisteredUsers = (): string => {
	return App.system.users.reduce((output, user) => {
		return html`
			${output}
			<span
				class="${classes.flex} ${classes.flexGap} ${classes.flexAlignCenter}"
			>
				<span class="${classes.flexItemGrow}">
					<i class="bi bi-person"></i>
					${user.name} (${user.email})
				</span>
				<span>
					${App.user.name == user.name
						? App.text('systemUsersYou')
						: html`<input
								type="checkbox"
								name="delete[${user.name}]"
						  />`}
				</span>
			</span>
		`;
	}, '');
};

/**
 * The delete users form.
 *
 * @extends FormComponent
 */
export class DeleteUsersComponent extends FormComponent {
	/**
	 * Initialize the form.
	 */
	protected init(): void {
		const render = () => {
			this.innerHTML = renderRegisteredUsers();
		};

		super.init();

		this.listeners.push(
			listen(window, eventNames.appStateChange, render.bind(this))
		);

		render();
	}
}

customElements.define('am-delete-users', DeleteUsersComponent);
