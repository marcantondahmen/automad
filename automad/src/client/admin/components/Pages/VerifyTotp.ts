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
							class="${CSS.input}"
							name="code"
							type="text"
							maxlength="6"
							inputmode="numeric"
							autocomplete="one-time-code"
							pattern="[0-9]{6}"
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
								${App.text('verifyTotpButton')}
							</am-submit>
						</div>
					</div>
				</div>
			</am-form>
			<am-form ${Attr.api}="${SessionController.cancelTotpVerification}">
				<am-submit class="${CSS.button}">
					${App.text('cancel')}
				</am-submit>
			</am-form>
		`;
	}
}

customElements.define(getTagFromRoute(Route.verifytotp), VerifyTotpComponent);
