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
 * Copyright (c) 2021-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, CSS, getTagFromRoute, html, Routes } from '../../core';
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
			<h1>$${App.sitename} &mdash; $${App.text('signIn')}</h1>
			<div class="am-c-card">
				<am-form
					class="am-c-card__form"
					${Attr.api}="Session/login"
					${Attr.focus}
					${Attr.enter}
				>
					<input
						class="${CSS.input}"
						type="text"
						name="name-or-email"
						placeholder="$${App.text('usernameOrEmail')}"
						required
					/>
					<input
						class="${CSS.input}"
						type="password"
						name="password"
						placeholder="$${App.text('password')}"
						required
					/>
					<div class="am-c-card__form-buttons">
						<a
							href="./${Routes.resetpassword}"
							class="${CSS.button}"
						>
							$${App.text('forgotPassword')}
						</a>
						<am-submit
							class="${CSS.button} ${CSS.buttonPrimary}"
							${Attr.form}="Session/login"
						>
							$${App.text('signIn')}
						</am-submit>
					</div>
				</am-form>
			</div>
		`;
	}
}

customElements.define(getTagFromRoute(Routes.login), LoginComponent);
