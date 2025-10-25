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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	confirm,
	create,
	createField,
	createSelect,
	CSS,
	EventName,
	FieldTag,
	fire,
	MailConfigController,
	requestAPI,
	Undo,
} from '@/admin/core';
import { MailConfig } from '@/admin/types';
import { FormComponent } from './Form';

export const transportOptions = ['sendmail', 'smtp'] as const;

/**
 * The mail config component.
 *
 * @extends FormComponent
 */
export class MailConfigFormComponent extends FormComponent {
	/**
	 * Only enable submit button when input values have changed.
	 */
	protected get watch(): boolean {
		return true;
	}

	/**
	 * The configuration that was used to render a form.
	 */
	private get config() {
		return this._config;
	}

	/**
	 * The configuration that was used to render a form.
	 */
	private set config(data: MailConfig) {
		this._config = data;
		this.render();
	}

	/**
	 * The configuration that was used to render a form.
	 */
	private _config: MailConfig = null;

	/**
	 * The function that is called when the form is connected.
	 */
	protected init(): void {
		this.config = App.system.mail;

		this.listen(window, EventName.appStateChange, () => {
			this.config = App.system.mail;
		});
	}

	/**
	 * Render the form.
	 */
	private render(): void {
		const data = this.config;

		this.innerHTML = '';
		this.classList.add(CSS.flex, CSS.flexColumn, CSS.flexGapLarge);

		const mailFields = create('div', [], {}, this);

		const transportSelect = createSelect(
			transportOptions.map((opt) => ({ value: opt })),
			data.transport,
			mailFields,
			'transport'
		);

		createField(FieldTag.email, mailFields, {
			key: 'from',
			name: 'from',
			value: data.from,
			label: `${App.text('emailFrom')} (From)`,
			placeholder: data.fromDefault,
		});

		const smtpCard = create('div', [CSS.card], {}, this);

		const toggleSmtpCard = () => {
			smtpCard.classList.toggle(
				CSS.displayNone,
				transportSelect.value !== 'smtp'
			);
		};

		const smtpCardForm = create('div', [CSS.cardForm], {}, smtpCard);
		const smtpCardGrid = create(
			'div',
			[CSS.grid, CSS.gridAuto],
			{},
			smtpCardForm
		);

		createField(
			FieldTag.input,
			smtpCardGrid,
			{
				key: 'smtpServer',
				name: 'smtpServer',
				value: data.smtpServer,
				label: 'SMTP Server',
			},
			[],
			{ pattern: '^\\S*$' }
		);

		createField(
			FieldTag.input,
			smtpCardGrid,
			{
				key: 'smtpPort',
				name: 'smtpPort',
				value: data.smtpPort,
				label: 'SMTP Port',
			},
			[],
			{ pattern: '^\\d{0,5}$' }
		);

		createField(
			FieldTag.input,
			smtpCardForm,
			{
				key: 'smtpUsername',
				name: 'smtpUsername',
				value: data.smtpUsername,
				label: `SMTP ${App.text('username')}`,
			},
			[],
			{ pattern: '^\\S*$' }
		);

		createField(FieldTag.input, smtpCardForm, {
			key: 'smtpPassword',
			name: 'smtpPassword',
			value: '',
			label: `SMTP ${App.text('password')}`,
			placeholder: App.system.mail.smtpPasswordIsSet
				? App.text('systemMailSmtpPasswordPlaceholder')
				: '',
		});

		toggleSmtpCard();

		this.listen(transportSelect, 'change', toggleSmtpCard);

		const footer = create('div', [CSS.flex, CSS.flexGap], {}, this);

		create(
			'am-submit',
			[CSS.button, CSS.buttonPrimary],
			{ disabled: 'true' },
			footer,
			App.text('save')
		);

		const reset = create(
			'button',
			[CSS.button, CSS.buttonDanger],
			{},
			footer,
			App.text('reset')
		);

		this.listen(reset, 'click', async () => {
			if (!(await confirm(App.text('systemMailReset')))) {
				return;
			}

			requestAPI(MailConfigController.reset, {}, true, () => {
				fire(EventName.appStateRequireUpdate);
				Undo.new();
			});
		});
	}
}

customElements.define('am-mail-config-form', MailConfigFormComponent);
