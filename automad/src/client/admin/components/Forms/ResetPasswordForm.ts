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

import { App, classes, createField, html, Routes } from '../../core';
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
			<p>${App.text('passwordResetEnterNameOrEmail')}</p>
			${createField(
				'am-input',
				null,
				{
					key: 'name-or-email',
					name: 'name-or-email',
					value: searchParams.get('username') || '',
					label: App.text('usernameOrEmail'),
				},
				[],
				{}
			).outerHTML}
			<am-submit class="${classes.button}">
				${App.text('submit')}
			</am-submit>
		`;
	}

	/**
	 * Render the password form.
	 *
	 * @param data
	 */
	private renderSetPassword(data: KeyValueMap): void {
		this.innerHTML = html`
			<p>${App.text('passwordResetEnterNewPassword')}</p>
			<input type="hidden" name="username" value="${data.username}" />
			${createField(
				'am-input',
				null,
				{
					key: 'token',
					name: 'token',
					value: '',
					label: App.text('passwordResetToken'),
				},
				[],
				{ required: '' }
			).outerHTML}
			${createField(
				'am-password',
				null,
				{
					key: 'password1',
					name: 'password1',
					value: '',
					label: App.text('password'),
				},
				[],
				{ required: '' }
			).outerHTML}
			${createField(
				'am-password',
				null,
				{
					key: 'password2',
					name: 'password2',
					value: '',
					label: App.text('repeatPassword'),
				},
				[],
				{ required: '' }
			).outerHTML}
			<am-submit class="${classes.button}">
				${App.text('passwordResetSave')}
			</am-submit>
		`;
	}

	/**
	 * Render the success message.
	 *
	 * @param data
	 */
	private renderSuccess(data: KeyValueMap): void {
		this.innerHTML = html`
			<p>${App.text('passwordChangedSuccess')}</p>
			<am-link class="${classes.button}" target="${Routes.login}">
				$${App.text('signIn')}
			</am-link>
		`;
	}
}

customElements.define('am-reset-password-form', ResetPasswordFormComponent);
