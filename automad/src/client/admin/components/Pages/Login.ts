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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	CSS,
	getTagFromRoute,
	html,
	Route,
	SessionController,
} from '@/admin/core';
import { BaseCenteredLayoutComponent } from './BaseCenteredLayout';

/**
 * The login view.
 *
 * @extends BaseCenteredLayoutComponent
 */
export class LoginComponent extends BaseCenteredLayoutComponent {
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
			<h2>$${App.sitename} &mdash; ${App.text('signIn')}</h2>
			<am-form
				${Attr.api}="${SessionController.login}"
				${Attr.focus}
				${Attr.enter}
			>
				<am-form-error></am-form-error>
				<div class="${CSS.card}">
					<div class="${CSS.cardForm}">
						<input
							class="${CSS.input}"
							type="text"
							name="name-or-email"
							placeholder="${App.text('usernameOrEmail')}"
							required
						/>
						<input
							class="${CSS.input}"
							type="password"
							name="password"
							placeholder="${App.text('password')}"
							required
						/>
						<div class="${CSS.cardFormButtons}">
							<am-link
								${Attr.target}="${Route.resetpassword}"
								class="${CSS.button}"
							>
								${App.text('forgotPassword')}
							</am-link>
							<am-submit
								class="${CSS.button} ${CSS.buttonPrimary}"
							>
								${App.text('signIn')}
							</am-submit>
						</div>
					</div>
				</div>
			</am-form>
		`;
	}
}

customElements.define(getTagFromRoute(Route.login), LoginComponent);
