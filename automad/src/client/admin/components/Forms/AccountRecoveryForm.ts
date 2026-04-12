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

/**
 * The delete users form.
 *
 * @extends FormComponent
 */
export class AccountRecoveryFormComponent extends FormComponent {
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
		this.innerHTML = html`
			<h2>${App.text('accountRecoveryStartHeading')}</h2>
			<am-form-error></am-form-error>
			<div class="${CSS.card}">
				<div class="${CSS.cardBody} ${CSS.cardBodyLarge}">
					${App.text('accountRecoveryStartText')}
				</div>
				<div class="${CSS.cardForm}">
					<input
						type="text"
						class="${CSS.input}"
						name="name-or-email"
						value="$${getSearchParam('username')}"
						placeholder="${App.text('usernameOrEmail')}"
					/>
					<div class="${CSS.cardFormButtons}">
						<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
							${App.text('accountRecoveryStartButton')}
						</am-submit>
					</div>
				</div>
			</div>
			<p>
				<am-link class="${CSS.link}" ${Attr.target}="${Route.login}">
					${App.text('accountRecoveryCancel')}
				</am-link>
			</p>
		`;
	}

	/**
	 * Render the password form.
	 *
	 * @param data
	 */
	private renderSetPassword(data: KeyValueMap): void {
		this.innerHTML = html`
			<h2>${App.text('accountRecoveryNewPasswordHeading')}</h2>
			<am-form-error></am-form-error>
			<div class="${CSS.card}">
				<div class="${CSS.cardBody} ${CSS.cardBodyLarge}">
					${App.text('accountRecoveryNewPasswordText')}
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
			<p>
				<am-link class="${CSS.link}" ${Attr.target}="${Route.login}">
					${App.text('accountRecoveryCancel')}
				</am-link>
			</p>
		`;
	}

	/**
	 * Render the success message.
	 *
	 * @param data
	 */
	private renderSuccess(data: KeyValueMap): void {
		this.innerHTML = html`
			<h2>${App.text('accountRecoverySuccessHeading')}</h2>
			<div class="${CSS.card}">
				<div class="${CSS.cardBody} ${CSS.cardBodyLarge}">
					${App.text('accountRecoverySuccessText')}
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

customElements.define('am-account-recovery-form', AccountRecoveryFormComponent);
