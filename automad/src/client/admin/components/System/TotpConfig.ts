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

import { BaseComponent } from '@/admin/components/Base';
import { create, query, UserController } from '@/common';
import {
	App,
	Attr,
	confirm,
	CSS,
	EventName,
	fire,
	getComponentTargetContainer,
	html,
	notifyError,
	notifySuccess,
	requestAPI,
} from '@/admin/core';
import { ModalComponent } from '@/admin/components/Modal/Modal';

/**
 * A TOTP config component.
 *
 * @extends BaseComponent
 */
class TotpConfigComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 *
	 * @async
	 */
	async connectedCallback(): Promise<void> {
		const { data } = await requestAPI(UserController.totpIsConfigured);

		if (data.totpIsConfigured) {
			await this.disable();

			return;
		}

		await this.setup();
	}

	/**
	 * Start the 2FA setup.
	 *
	 * @async
	 */
	private async setup(): Promise<void> {
		this.innerHTML = html`
			<p>
				<am-icon-text
					${Attr.icon}="shield-slash"
					${Attr.text}="${App.text('systemUsersTotpIsDisabled')}"
				></am-icon-text>
			</p>
		`;

		const modalButton = create(
			'button',
			[CSS.button, CSS.buttonPrimary],
			{},
			this,
			App.text('systemUsersTotpConfigure')
		);

		this.listen(modalButton, 'click', this.renderSetupModal.bind(this));
	}

	/**
	 * Render the setup modal dialog.
	 *
	 * @async
	 */
	private async renderSetupModal(): Promise<void> {
		const modal = create(
			ModalComponent.TAG_NAME,
			[],
			{},
			getComponentTargetContainer(),
			html`
				<am-modal-dialog class="${CSS.modalDialogTotp}">
					<am-modal-header>
						${App.text('systemUsersTotpConfigure')}
					</am-modal-header>
					<am-modal-body>
						<am-spinner></am-spinner>
					</am-modal-body>
				</am-modal-dialog>
			`
		) as ModalComponent;

		const body = query('am-modal-body', modal);

		setTimeout(async () => {
			modal.open();

			const { data } = await requestAPI(UserController.totpSetup);

			body.innerHTML = '';

			const totp = create('div', [CSS.totp], {}, body);

			create('img', [CSS.totpQr], { src: data.qr }, totp);

			const secretBox = create(
				'input',
				[CSS.totpSecret],
				{ readonly: '', value: data.secret },
				totp
			);

			this.listen(secretBox, 'click', () => {
				secretBox.select();
			});

			create(
				'p',
				[],
				{},
				body,
				App.text('systemUsersTotpConfigureDialogText')
			);

			const form = create(
				'div',
				[CSS.flex, CSS.flexColumn, CSS.flexGap],
				{},
				body
			);

			const input = create(
				'input',
				[CSS.input, CSS.inputTotp],
				{
					maxlength: 6,
					pattern: '[0-9]{6}',
					inputmode: 'numeric',
					autocomplete: 'one-time-code',
				},
				form
			);

			input.focus();

			this.listen(input, 'input', () => {
				if (input.value.length == 6) {
					button.disabled = false;
				} else {
					button.disabled = true;
				}
			});

			const button = create(
				'button',
				[CSS.button, CSS.buttonPrimary, CSS.flexItemGrow],
				{ disabled: '' },
				form,
				App.text('systemUsersTotpConfigureDialogButton')
			);

			this.listen(button, 'click', async () => {
				button.classList.add(CSS.buttonLoading);

				const response = await requestAPI(
					UserController.totpConfirmSetup,
					{ code: input.value }
				);

				button.classList.remove(CSS.buttonLoading);

				if (response.data?.confirmed) {
					notifySuccess(App.text('systemUsersTotpConfigureSuccess'));
					fire(EventName.appStateRequireUpdate);

					modal.close();

					setTimeout(() => {
						this.disable();
					}, 400);

					return;
				}

				notifyError(response.error);
			});
		}, 0);
	}

	/**
	 * Disable 2FA.
	 *
	 * @async
	 */
	private async disable(): Promise<void> {
		this.innerHTML = html`
			<p>
				<am-icon-text
					${Attr.icon}="shield-fill-check"
					${Attr.text}="${App.text('systemUsersTotpIsEnabled')}"
				></am-icon-text>
			</p>
		`;

		const disableButton = create(
			'button',
			[CSS.button, CSS.buttonDanger],
			{},
			this,
			App.text('systemUsersTotpDisable')
		);

		this.listen(disableButton, 'click', async () => {
			if (
				!(await confirm(
					App.text('systemUsersTotpDisableConfirmText'),
					App.text('systemUsersTotpDisableConfirmButton')
				))
			) {
				return;
			}

			disableButton.classList.add(CSS.buttonLoading);

			const response = await requestAPI(UserController.totpDisable, {
				disableTotp: 1,
			});

			disableButton.classList.remove(CSS.buttonLoading);

			if (response.data?.disabled) {
				notifySuccess(App.text('systemUsersTotpDisabledSuccessfully'));
				fire(EventName.appStateRequireUpdate);
				this.setup();
			}
		});
	}
}

customElements.define('am-totp-config', TotpConfigComponent);
