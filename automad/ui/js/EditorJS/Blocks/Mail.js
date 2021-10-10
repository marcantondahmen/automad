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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

class AutomadBlockMail {
	static get isReadOnlySupported() {
		return true;
	}

	static get sanitize() {
		return {
			to: false,
			placeholderEmail: false,
			placeholderSubject: false,
			placeholderMessage: false,
		};
	}

	static get toolbox() {
		return {
			title: AutomadEditorTranslation.get('mail_toolbox'),
			icon: '<svg width="18px" height="15px" viewBox="0 0 18 15"><path d="M14,0H4C1.791,0,0,1.791,0,4v7c0,2.209,1.791,4,4,4h10c2.209,0,4-1.791,4-4V4C18,1.791,16.209,0,14,0z M14,2 c0.153,0,0.302,0.021,0.445,0.054L9,7.5L3.554,2.054C3.698,2.021,3.846,2,4,2H14z M16,11c0,1.103-0.897,2-2,2H4 c-1.103,0-2-0.897-2-2V4c0-0.154,0.021-0.302,0.054-0.446L8.25,9.75C8.457,9.957,8.729,10.061,9,10.061S9.543,9.957,9.75,9.75 l6.195-6.196C15.979,3.698,16,3.846,16,4V11z"/></svg>',
		};
	}

	constructor({ data }) {
		var create = Automad.Util.create,
			t = AutomadEditorTranslation.get;

		this.data = {
			to: data.to || '',
			error: data.error || t('mail_data_error'),
			success: data.success || t('mail_data_success'),
			placeholderEmail:
				data.placeholderEmail || t('mail_data_placeholder_address'),
			placeholderSubject:
				data.placeholderSubject || t('mail_data_placeholder_subject'),
			placeholderMessage:
				data.placeholderMessage || t('mail_data_placeholder_message'),
			textButton: data.textButton || t('mail_data_send'),
		};

		this.inputs = {
			to: create.editable(
				['cdx-input'],
				t('mail_placeholder_address'),
				this.data.to
			),
			error: create.editable(
				['cdx-input'],
				t('mail_placeholder_error'),
				this.data.error
			),
			success: create.editable(
				['cdx-input'],
				t('mail_placeholder_success'),
				this.data.success
			),
			placeholderEmail: create.editable(
				['cdx-input'],
				'',
				this.data.placeholderEmail
			),
			placeholderSubject: create.editable(
				['cdx-input'],
				'',
				this.data.placeholderSubject
			),
			placeholderMessage: create.editable(
				['cdx-input'],
				'',
				this.data.placeholderMessage
			),
			textButton: create.editable(
				['cdx-input'],
				'',
				this.data.textButton
			),
		};

		var icon = document.createElement('div'),
			title = document.createElement('div');

		icon.innerHTML = AutomadBlockMail.toolbox.icon;
		icon.classList.add('am-block-icon');
		title.innerHTML = AutomadBlockMail.toolbox.title;
		title.classList.add('am-block-title');

		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('uk-panel', 'uk-panel-box');
		this.wrapper.appendChild(icon);
		this.wrapper.appendChild(title);
		this.wrapper.appendChild(document.createElement('hr'));
		this.wrapper.appendChild(create.label(t('mail_label_address')));
		this.wrapper.appendChild(this.inputs.to);
		this.wrapper.appendChild(create.label(t('mail_label_success')));
		this.wrapper.appendChild(this.inputs.success);
		this.wrapper.appendChild(create.label(t('mail_label_error')));
		this.wrapper.appendChild(this.inputs.error);
		this.wrapper.appendChild(
			create.label(t('mail_label_placeholder_address'))
		);
		this.wrapper.appendChild(this.inputs.placeholderEmail);
		this.wrapper.appendChild(
			create.label(t('mail_label_placeholder_subject'))
		);
		this.wrapper.appendChild(this.inputs.placeholderSubject);
		this.wrapper.appendChild(
			create.label(t('mail_label_placeholder_message'))
		);
		this.wrapper.appendChild(this.inputs.placeholderMessage);
		this.wrapper.appendChild(create.label(t('mail_label_button')));
		this.wrapper.appendChild(this.inputs.textButton);
	}

	render() {
		return this.wrapper;
	}

	save() {
		var stripNbsp = Automad.Util.stripNbsp;

		return Object.assign(this.data, {
			to: stripNbsp(this.inputs.to.innerHTML),
			error: stripNbsp(this.inputs.error.innerHTML),
			success: stripNbsp(this.inputs.success.innerHTML),
			placeholderEmail: stripNbsp(this.inputs.placeholderEmail.innerHTML),
			placeholderSubject: stripNbsp(
				this.inputs.placeholderSubject.innerHTML
			),
			placeholderMessage: stripNbsp(
				this.inputs.placeholderMessage.innerHTML
			),
			textButton: stripNbsp(this.inputs.textButton.innerHTML),
		});
	}
}
