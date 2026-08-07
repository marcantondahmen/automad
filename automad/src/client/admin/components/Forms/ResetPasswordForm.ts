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
	routes,
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
				newPasswordCode: App.text(
					'completeAccountSetupNewPasswordCode'
				),
				newPasswordEnter: App.text(
					'completeAccountSetupNewPasswordEnter'
				),
				successHeading: App.text('completeAccountSetupSuccessHeading'),
				successText: App.text('completeAccountSetupSuccessText'),
			}
		: {
				newPasswordHeading: App.text(
					'accountRecoveryNewPasswordHeading'
				),
				newPasswordText: App.text('accountRecoveryNewPasswordText'),
				newPasswordCode: App.text('accountRecoveryNewPasswordCode'),
				newPasswordEnter: App.text('accountRecoveryNewPasswordEnter'),
				successHeading: App.text('accountRecoverySuccessHeading'),
				successText: App.text('accountRecoverySuccessText'),
			};
};

const cancel = () => {
	return isInvite()
		? ''
		: html`
				<am-link class="${CSS.link}" ${Attr.target}="${routes.login}">
					${App.text('accountRecoveryCancel')}
				</am-link>
			`;
};

/**
 * The password reset form.
 *
 * @extends FormComponent
 */
class ResetPasswordFormComponent extends FormComponent {
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
		this.innerHTML = html`
			<h2>${text().newPasswordHeading}</h2>
			<am-form-error></am-form-error>
			<div class="${CSS.card}">
				<div class="${CSS.cardBody} ${CSS.cardBodyLarge}">
					${text().newPasswordText}
				</div>
				<div class="${CSS.cardForm}">
					<div class="${CSS.textParagraph}">
						<p>${text().newPasswordCode}</p>
					</div>
					<input
						class="${CSS.input} ${CSS.inputResetCode}"
						type="text"
						name="code"
						maxlength="8"
						pattern="[0-9A-Z]{8}"
						required
					/>
				</div>
				<div class="${CSS.cardForm}">
					<div class="${CSS.textParagraph}">
						<p>${text().newPasswordEnter}</p>
					</div>
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
			<p class="${CSS.flex} ${CSS.flexColumn}">
				${cancel()}
				<am-link
					class="${CSS.link}"
					${Attr.target}="${routes.getVerificationCode}${window
						.location.search}"
				>
					${App.text('passwordResetCodeResend')}
				</am-link>
			</p>
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
						${Attr.target}="${routes.login}"
					>
						${App.text('signIn')}
					</am-link>
				</div>
			</div>
		`;
	}
}

customElements.define('am-reset-password-form', ResetPasswordFormComponent);
