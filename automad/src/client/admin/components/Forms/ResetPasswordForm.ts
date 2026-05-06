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

import { App, Attr, CSS, getSearchParam, html, Route } from '@/admin/core';
import { KeyValueMap } from '@/admin/types';
import { FormComponent } from './Form';

export const isInvite = () => {
	return getSearchParam('action') === 'create';
};

const text = () => {
	return isInvite()
		? {
				newPasswordHeading: App.text(
					'createPasswordNewPasswordHeading'
				),
				newPasswordText: App.text('createPasswordNewPasswordText'),
				startButton: App.text('createPasswordStartButton'),
				startHeading: App.text('createPasswordStartHeading'),
				startText: App.text('createPasswordStartText'),
				successHeading: App.text('createPasswordSuccessHeading'),
				successText: App.text('createPasswordSuccessText'),
			}
		: {
				newPasswordHeading: App.text('resetPasswordNewPasswordHeading'),
				newPasswordText: App.text('resetPasswordNewPasswordText'),
				startButton: App.text('resetPasswordStartButton'),
				startHeading: App.text('resetPasswordStartHeading'),
				startText: App.text('resetPasswordStartText'),
				successHeading: App.text('resetPasswordSuccessHeading'),
				successText: App.text('resetPasswordSuccessText'),
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
						${App.text('resetPasswordCancel')}
					</am-link>
				</p>
			`;
};

/**
 * The delete users form.
 *
 * @extends FormComponent
 */
export class ResetPasswordFormComponent extends FormComponent {
	/**
	 * The states object maps states to render methods.
	 */
	private get states(): KeyValueMap {
		return {
			success: this.renderSuccess,
			setPassword: this.renderSetPassword,
			requestToken: this.renderRequestToken,
		};
	}

	/**
	 * The form inits itself when created.
	 */
	protected get initSelf(): boolean {
		return true;
	}

	/**
	 * Process the response that is received after submitting the form.
	 *
	 * @param response
	 * @async
	 */
	protected async processResponse(response: KeyValueMap): Promise<void> {
		if (!response.data) {
			return;
		}

		const data = response.data;

		if (data.state && Object.keys(this.states).includes(data.state)) {
			this.states[data.state].apply(this, [data]);
		}
	}

	/**
	 * Render the token request form.
	 *
	 * @param data
	 */
	private renderRequestToken(data: KeyValueMap): void {
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
	 * Render the password form.
	 *
	 * @param data
	 */
	private renderSetPassword(data: KeyValueMap): void {
		this.innerHTML = html`
			<h2>${text().newPasswordHeading}</h2>
			<am-form-error></am-form-error>
			<div class="${CSS.card}">
				<div class="${CSS.cardBody} ${CSS.cardBodyLarge}">
					${text().newPasswordText}
				</div>
				<div class="${CSS.cardForm}">
					<input
						type="text"
						class="${CSS.input}"
						name="token"
						autocomplete="reset-token"
						placeholder="${App.text('passwordResetToken')}"
						${Attr.tooltip}="${App.text('passwordResetToken')}"
						${Attr.tooltipOptions}="placement: top"
						required
					/>
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
					<input
						type="hidden"
						name="username"
						value="${data.username}"
					/>
					<div class="${CSS.cardFormButtons}">
						<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
							${App.text('passwordResetSave')}
						</am-submit>
					</div>
				</div>
			</div>
			${cancel()}
		`;
	}

	/**
	 * Render the success message.
	 *
	 * @param data
	 */
	private renderSuccess(data: KeyValueMap): void {
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
}

customElements.define('am-reset-password-form', ResetPasswordFormComponent);
