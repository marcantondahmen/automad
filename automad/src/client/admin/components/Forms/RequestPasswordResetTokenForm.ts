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
	getSearchParam,
	html,
	isInvite,
	Route,
} from '@/admin/core';
import { KeyValueMap } from '@/admin/types';
import { FormComponent } from './Form';

const text = () => {
	return isInvite()
		? {
				startButton: App.text('completeAccountSetupRequestTokenButton'),
				startHeading: App.text(
					'completeAccountSetupRequestTokenHeading'
				),
				startText: App.text('completeAccountSetupRequestTokenText'),
				successHeading: App.text(
					'completeAccountSetupTokenSentHeading'
				),
				successText: App.text('completeAccountSetupTokenSentText'),
			}
		: {
				startButton: App.text('accountRecoveryRequestTokenButton'),
				startHeading: App.text('accountRecoveryRequestTokenHeading'),
				startText: App.text('accountRecoveryRequestTokenText'),
				successHeading: App.text('accountRecoveryTokenSentHeading'),
				successText: App.text('accountRecoveryTokenSentText'),
			};
};

const cancel = () => {
	return isInvite()
		? ''
		: html`
				<p>
					<am-link
						class="${CSS.link}"
						${Attr.target}="${Route.login}"
					>
						${App.text('accountRecoveryCancel')}
					</am-link>
				</p>
			`;
};

/**
 * The token request form.
 *
 * @extends FormComponent
 */
export class RequestPasswordResetTokenFormComponent extends FormComponent {
	/**
	 * Process the response that is received after submitting the form.
	 *
	 * @param response
	 * @async
	 */
	protected async processResponse(response: KeyValueMap): Promise<void> {
		if (response.data?.success) {
			this.renderSuccess();
		}
	}

	/**
	 * Render the initial form.
	 */
	protected init(): void {
		const inputInvite = isInvite()
			? html`
					<input
						type="hidden"
						name="name-or-email"
						value="$${getSearchParam('username')}"
					/>
				`
			: '';

		const inputReset = isInvite()
			? ''
			: html`
					<input
						type="text"
						class="${CSS.input}"
						name="name-or-email"
						placeholder="${App.text('usernameOrEmail')}"
						required
					/>
				`;

		this.innerHTML = html`
			<h2>${text().startHeading}</h2>
			<am-form-error></am-form-error>
			<div class="${CSS.card}">
				<div class="${CSS.cardBody} ${CSS.cardBodyLarge}">
					${text().startText}
				</div>
				<input
					type="hidden"
					name="type"
					value="${getSearchParam('type')}"
				/>
				${inputInvite}
				<div class="${CSS.cardForm}">
					${inputReset}
					<div class="${CSS.cardFormButtons}">
						<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
							${text().startButton}
						</am-submit>
					</div>
				</div>
			</div>
			${cancel()}
		`;
	}

	/**
	 * Render the success message.
	 */
	private renderSuccess(): void {
		this.innerHTML = html`
			<h2>${text().successHeading}</h2>
			<div class="${CSS.card}">
				<div class="${CSS.cardBody} ${CSS.cardBodyLarge}">
					${text().successText}
				</div>
			</div>
		`;
	}
}

customElements.define(
	'am-request-password-reset-token-form',
	RequestPasswordResetTokenFormComponent
);
