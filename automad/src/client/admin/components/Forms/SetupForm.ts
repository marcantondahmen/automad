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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	Attr,
	create,
	CSS,
	html,
	InputPattern,
	KeyValueMap,
	Route,
} from '@/admin/core';
import { FormComponent } from './Form';

/**
 * Create the first user account.
 *
 * @extends FormComponent
 */
export class SetupFormComponent extends FormComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	protected init(): void {
		this.renderForm();
	}

	/**
	 * Process the response that is received after submitting the form.
	 *
	 * @param response
	 * @async
	 */
	protected async processResponse(response: KeyValueMap): Promise<void> {
		const { data } = response;

		if (data?.php && data?.filename && data?.configDir) {
			this.renderSuccess(data.configDir, data.filename);

			const href = window.URL.createObjectURL(
				new Blob([data.php], { type: 'application/octet-stream' })
			);

			create('a', [], {
				href,
				target: '_blank',
				download: data.filename,
			}).click();
		}
	}

	/**
	 * Render the account data form.
	 */
	private renderForm(): void {
		// Setup happens before a user can set a language.
		// Therefore all text modules will be by default in English.
		this.innerHTML = html`
			<h2>Create User &mdash; Step 1/2</h2>
			<am-form-error></am-form-error>
			<div class="${CSS.card}">
				<div class="${CSS.cardBody} ${CSS.cardBodyLarge}">
					<p>
						Create the first user account using the form below and
						download the account file to your computer.
					</p>
				</div>
				<div class="${CSS.cardForm}">
					<input
						class="${CSS.input}"
						type="text"
						name="username"
						pattern="${InputPattern.username}"
						placeholder="Username"
						${Attr.tooltip}='A username must start and end with a letter or number and can only contain the characters "a-z", "0-9", "_" or "-".'
						${Attr.tooltipOptions}="placement:right"
						required
					/>
					<input
						class="${CSS.input}"
						type="email"
						name="email"
						placeholder="Email"
						required
					/>
					<input
						class="${CSS.input}"
						type="password"
						name="password1"
						placeholder="Password"
						required
					/>
					<input
						class="${CSS.input}"
						type="password"
						name="password2"
						placeholder="Repeat Password"
						required
					/>
					<div class="${CSS.cardFormButtons}">
						<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
							Create Account and Download File
						</am-submit>
					</div>
				</div>
			</div>
		`;
	}

	/**
	 * Render the second step of the process.
	 *
	 * @param configDir
	 * @param filename
	 */
	private renderSuccess(configDir: string, filename: string): void {
		this.innerHTML = html`
			<h2>Create User &mdash; Step 2/2</h2>
			<div class="${CSS.card}">
				<div class="${CSS.cardBody} ${CSS.cardBodyLarge}">
					<p>
						Now, upload the downloaded <code>${filename}</code> to
						your webserver and place it inside
						<code>${configDir}</code>. After the file is uploaded,
						hit the button below in order to sign in.
					</p>
				</div>
				<div class="${CSS.cardForm}">
					<div class="${CSS.cardFormButtons}">
						<a
							href="./${Route.login}"
							class="${CSS.button} ${CSS.buttonPrimary}"
						>
							I have uploaded the file, let me sign in!
						</a>
					</div>
				</div>
			</div>
		`;
	}
}

customElements.define('am-setup-form', SetupFormComponent);
