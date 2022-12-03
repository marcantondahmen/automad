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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, createField, CSS, html, Routes } from '../../core';
import { KeyValueMap } from '../../types';
import { FormComponent } from './Form';

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
		super.processResponse(response);

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
		const searchParams = new URLSearchParams(window.location.search);

		this.innerHTML = html`
			<h1>$${App.text('resetPassword')}</h1>
			<div class="${CSS.card}">
				<div class="${CSS.cardBody} ${CSS.cardBodyLarge}">
					$${App.text('passwordResetEnterNameOrEmail')}
				</div>
				<div class="${CSS.cardForm}">
					<input
						type="text"
						class="${CSS.input}"
						name="name-or-email"
						value="$${searchParams.get('username') || ''}"
						placeholder="$${App.text('usernameOrEmail')}"
					/>
					<div class="${CSS.cardFormButtons}">
						<am-link class="${CSS.button}" target="${Routes.login}">
							$${App.text('cancel')}
						</am-link>
						<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
							$${App.text('submit')}
						</am-submit>
					</div>
				</div>
			</div>
		`;
	}

	/**
	 * Render the password form.
	 *
	 * @param data
	 */
	private renderSetPassword(data: KeyValueMap): void {
		this.innerHTML = html`
			<h1>$${App.text('resetPassword')}</h1>
			<div class="${CSS.card}">
				<div class="${CSS.cardBody} ${CSS.cardBodyLarge}">
					$${App.text('passwordResetEnterNewPassword')}
				</div>
				<div class="${CSS.cardForm}">
					<input
						type="text"
						class="${CSS.input}"
						name="token"
						placeholder="$${App.text('passwordResetToken')}"
						am-tooltip="$${App.text('passwordResetToken')}"
						am-tooltip-options="placement: top"
						required
					/>
					<input
						type="password"
						class="${CSS.input}"
						name="password1"
						placeholder="$${App.text('password')}"
						am-tooltip="$${App.text('password')}"
						am-tooltip-options="placement: top"
						required
					/>
					<input
						type="password"
						class="${CSS.input}"
						name="password2"
						placeholder="$${App.text('repeatPassword')}"
						am-tooltip="$${App.text('repeatPassword')}"
						am-tooltip-options="placement: top"
						required
					/>
					<div class="${CSS.cardFormButtons}">
						<am-link class="${CSS.button}" target="${Routes.login}">
							$${App.text('cancel')}
						</am-link>
						<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
							$${App.text('passwordResetSave')}
						</am-submit>
					</div>
				</div>
			</div>
		`;
	}

	/**
	 * Render the success message.
	 *
	 * @param data
	 */
	private renderSuccess(data: KeyValueMap): void {
		this.innerHTML = html`
			<h1>$${App.text('resetPassword')}</h1>
			<div class="${CSS.card}">
				<div class="${CSS.cardBody} ${CSS.cardBodyLarge}">
					$${App.text('passwordChangedSuccess')}
				</div>
				<div class="${CSS.cardForm}">
					<am-link class="${CSS.button}" target="${Routes.login}">
						$${App.text('signIn')}
					</am-link>
				</div>
			</div>
		`;
	}
}

customElements.define('am-reset-password-form', ResetPasswordFormComponent);
