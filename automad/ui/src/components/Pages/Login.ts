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

import { App, getTagFromRoute, html, Routes } from '../../core';
import { CenteredLayoutComponent } from './CenteredLayout';

/**
 * The page view.
 *
 * @extends CenteredLayoutComponent
 */
export class LoginComponent extends CenteredLayoutComponent {
	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderMainPartial(): string {
		return html`
			<am-form api="Session/login" focus enter>
				<input
					class="am-e-input"
					type="text"
					name="name-or-email"
					placeholder="$${App.text('usernameOrEmail')}"
					required
				/>
				<input
					class="am-e-input"
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

	/**
	 * Render the navbar title partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderTitlePartial(): string {
		return App.text('signIn');
	}
}

customElements.define(getTagFromRoute(Routes[Routes.login]), LoginComponent);
