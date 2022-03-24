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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, classes, getTagFromRoute, html, Routes } from '../../core';
import { CenteredLayoutComponent } from './CenteredLayout';

/**
 * The page view.
 *
 * @extends CenteredLayoutComponent
 */
export class LoginComponent extends CenteredLayoutComponent {
	/**
	 * Set the page title that is used a document title suffix.
	 */
	protected get pageTitle(): string {
		return App.text('signIn');
	}

	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderMainPartial(): string {
		return html`
			<am-form api="Session/login" focus enter>
				<input
					class="${classes.input}"
					type="text"
					name="name-or-email"
					placeholder="$${App.text('usernameOrEmail')}"
					required
				/>
				<input
					class="${classes.input}"
					type="password"
					name="password"
					placeholder="$${App.text('password')}"
					required
				/>
				<a href="./resetpassword" class="am-e-button">
					$${App.text('forgotPassword')}
				</a>
				<am-submit class="am-e-button" form="Session/login">
					$${App.text('signIn')}
				</am-submit>
			</am-form>
		`;
	}
}

customElements.define(getTagFromRoute(Routes[Routes.login]), LoginComponent);
