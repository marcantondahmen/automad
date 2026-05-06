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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
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
 * The totp verify view.
 *
 * @extends BaseCenteredLayoutComponent
 */
export class VerifyTotpComponent extends BaseCenteredLayoutComponent {
	/**
	 * Set the page title that is used a document title suffix.
	 */
	protected get pageTitle(): string {
		return App.text('verifyTotpButton');
	}

	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderMainPartial(): string {
		return html`
			<h2>${App.text('verifyTotpHeading')}</h2>
			<am-form
				${Attr.api}="${SessionController.verifyTotp}"
				${Attr.focus}
				${Attr.enter}
			>
				<am-form-error></am-form-error>
				<div class="${CSS.card}">
					<div class="${CSS.cardForm}">
						<input
							class="${CSS.input} ${CSS.inputTotp}"
							name="code"
							type="text"
							maxlength="6"
							inputmode="numeric"
							autocomplete="one-time-code"
							pattern="[0-9]{6}"
							required
						/>
						<div class="${CSS.cardFormButtons}">
							<am-submit
								class="${CSS.button} ${CSS.buttonPrimary}"
							>
								${App.text('verifyTotpButton')}
							</am-submit>
						</div>
					</div>
				</div>
			</am-form>
			<p>
				<am-form
					${Attr.api}="${SessionController.cancelTotpVerification}"
				>
					<am-submit class="${CSS.link}">
						${App.text('verifyTotpCancel')}
					</am-submit>
				</am-form>
				<br />
				<am-link ${Attr.target}="${Route.password}" class="${CSS.link}">
					${App.text('troubleSigningIn')}
				</am-link>
			</p>
		`;
	}
}

customElements.define(getTagFromRoute(Route.verifytotp), VerifyTotpComponent);
