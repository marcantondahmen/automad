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
 * Copyright (c) 2022-2026 by Marc Anton Dahmen
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
				newPasswordHeading: App.text(
					'completeAccountSetupNewPasswordHeading'
				),
				newPasswordText: App.text(
					'completeAccountSetupNewPasswordText'
				),
				successHeading: App.text('completeAccountSetupSuccessHeading'),
				successText: App.text('completeAccountSetupSuccessText'),
				invalidHeading: App.text('completeAccountSetupInvalidHeading'),
				invalidText: App.text('completeAccountSetupInvalidText'),
				invalidButton: App.text('completeAccountSetupInvalidButton'),
			}
		: {
				newPasswordHeading: App.text(
					'accountRecoveryNewPasswordHeading'
				),
				newPasswordText: App.text('accountRecoveryNewPasswordText'),
				successHeading: App.text('accountRecoverySuccessHeading'),
				successText: App.text('accountRecoverySuccessText'),
				invalidHeading: App.text('accountRecoveryInvalidHeading'),
				invalidText: App.text('accountRecoveryInvalidText'),
				invalidButton: App.text('accountRecoveryInvalidButton'),
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
 * The password reset form.
 *
 * @extends FormComponent
 */
export class ResetPasswordFormComponent extends FormComponent {
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

		if (response.data?.invalid) {
			this.renderInvalid();
		}
	}

	/**
	 * Render the initial form.
	 */
	protected init(): void {
		this.innerHTML = html`
			<h2>${text().newPasswordHeading}</h2>
			<am-form-error></am-form-error>
			<div class="${CSS.card}">
				<div class="${CSS.cardBody} ${CSS.cardBodyLarge}">
					${text().newPasswordText}
				</div>
				<div class="${CSS.cardForm}">
					<input
						type="password"
						class="${CSS.input}"
						name="password1"
						autocomplete="new-password-1"
						placeholder="${App.text('password')}"
						${Attr.tooltip}="${App.text('password')}"
						${Attr.tooltipOptions}="placement: top"
						required
					/>
					<input
						type="password"
						class="${CSS.input}"
						name="password2"
						autocomplete="new-password-2"
						placeholder="${App.text('repeatPassword')}"
						${Attr.tooltip}="${App.text('repeatPassword')}"
						${Attr.tooltipOptions}="placement: top"
						required
					/>
					<div class="${CSS.cardFormButtons}">
						<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
							${App.text('passwordResetSave')}
						</am-submit>
					</div>
				</div>
			</div>
			${cancel()}
			<input
				type="hidden"
				name="token"
				value="${getSearchParam('token')}"
				required
			/>
			<input
				type="hidden"
				name="username"
				value="${getSearchParam('username')}"
			/>
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
				<div class="${CSS.cardForm}">
					<am-link
						class="${CSS.button} ${CSS.buttonPrimary}"
						${Attr.target}="${Route.login}"
					>
						${App.text('signIn')}
					</am-link>
				</div>
			</div>
		`;
	}

	/**
	 * Render the invalid token message.
	 */
	private renderInvalid(): void {
		this.innerHTML = html`
			<h2>${text().invalidHeading}</h2>
			<div class="${CSS.card}">
				<div class="${CSS.cardBody} ${CSS.cardBodyLarge}">
					${text().invalidText}
				</div>
				<div class="${CSS.cardForm}">
					<am-link
						class="${CSS.button} ${CSS.buttonPrimary}"
						${Attr.target}="${Route.token}"
					>
						${text().invalidButton}
					</am-link>
				</div>
			</div>
		`;
	}
}

customElements.define('am-reset-password-form', ResetPasswordFormComponent);
