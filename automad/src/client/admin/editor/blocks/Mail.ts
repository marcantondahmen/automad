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
	App,
	Attr,
	collectFieldData,
	createField,
	CSS,
	FieldTag,
	html,
	uniqueId,
} from '@/admin/core';
import { KeyValueMap, MailBlockData } from '@/admin/types';
import { BaseBlock } from './BaseBlock';

export class MailBlock extends BaseBlock<MailBlockData> {
	/**
	 * Sanitizer rules
	 */
	static get sanitize() {
		return {
			to: false,
			error: {},
			success: {},
			errorAddress: false,
			labelAddress: false,
			errorSubject: false,
			labelSubject: false,
			errorBody: false,
			labelBody: false,
			labelSend: false,
		};
	}

	/**
	 * Toolbox settings.
	 */
	static get toolbox() {
		return {
			title: App.text('mailBlockTitle'),
			icon: '<i class="bi bi-envelope"></i>',
		};
	}

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: MailBlockData): MailBlockData {
		return {
			to: data.to || App.user.email,
			error: data.error || '',
			success: data.success || '',
			errorAddress: data.errorAddress || '',
			labelAddress: data.labelAddress || '',
			errorSubject: data.errorSubject || '',
			labelSubject: data.labelSubject || '',
			errorBody: data.errorBody || '',
			labelBody: data.labelBody || '',
			labelSend: data.labelSend || '',
		};
	}

	/**
	 * Render the block.
	 *
	 * @return the rendered element
	 */
	render(): HTMLElement {
		const disabled: KeyValueMap = this.readOnly ? { disabled: '' } : {};

		this.wrapper.classList.add(CSS.flex, CSS.flexColumn, CSS.flexGap);
		this.wrapper.innerHTML = html`
			<span class="${CSS.textMuted} ${CSS.userSelectNone}">
				<am-icon-text
					${Attr.icon}="envelope"
					${Attr.text}="${MailBlock.toolbox.title}"
				></am-icon-text>
			</span>
			<div class="${CSS.card}">
				<div class="${CSS.cardForm}">
					${createField(
						// This must be a standard input in order to
						// prevent editorjs selection error.
						FieldTag.input,
						null,
						{
							name: 'to',
							key: uniqueId(),
							label: App.text('mailBlockTo'),
							value: this.data.to,
							placeholder: 'you@domain.com',
						},
						[],
						{
							[Attr.error]: App.text('emailRequiredError'),
							required: '',
							...disabled,
						}
					).outerHTML}
					<div
						class="${CSS.grid} ${CSS.gridAuto}"
						style="--min: 20rem;"
					>
						${createField(
							FieldTag.input,
							null,
							{
								name: 'labelAddress',
								key: uniqueId(),
								label: App.text('mailBlockAddressFieldLabel'),
								value: this.data.labelAddress,
								placeholder: App.text(
									'mailBlockDefaultLabelAddress'
								),
							},
							[],
							disabled
						).outerHTML}
						${createField(
							FieldTag.input,
							null,
							{
								name: 'errorAddress',
								key: uniqueId(),
								label: App.text('mailBlockAddressFieldError'),
								value: this.data.errorAddress,
								placeholder: App.text(
									'mailBlockDefaultErrorAddress'
								),
							},
							[],
							disabled
						).outerHTML}
						${createField(
							FieldTag.input,
							null,
							{
								name: 'labelSubject',
								key: uniqueId(),
								label: App.text('mailBlockSubjectFieldLabel'),
								value: this.data.labelSubject,
								placeholder: App.text(
									'mailBlockDefaultLabelSubject'
								),
							},
							[],
							disabled
						).outerHTML}
						${createField(
							FieldTag.input,
							null,
							{
								name: 'errorSubject',
								key: uniqueId(),
								label: App.text('mailBlockSubjectFieldError'),
								value: this.data.errorSubject,
								placeholder: App.text(
									'mailBlockDefaultErrorSubject'
								),
							},
							[],
							disabled
						).outerHTML}
						${createField(
							FieldTag.input,
							null,
							{
								name: 'labelBody',
								key: uniqueId(),
								label: App.text('mailBlockBodyFieldLabel'),
								value: this.data.labelBody,
								placeholder: App.text(
									'mailBlockDefaultLabelBody'
								),
							},
							[],
							disabled
						).outerHTML}
						${createField(
							FieldTag.input,
							null,
							{
								name: 'errorBody',
								key: uniqueId(),
								label: App.text('mailBlockBodyFieldError'),
								value: this.data.errorBody,
								placeholder: App.text(
									'mailBlockDefaultErrorBody'
								),
							},
							[],
							disabled
						).outerHTML}
					</div>
					${createField(
						FieldTag.input,
						null,
						{
							name: 'labelSend',
							key: uniqueId(),
							label: App.text('mailBlockSendButtonLabel'),
							value: this.data.labelSend,
							placeholder: App.text('mailBlockDefaultLabelSend'),
						},
						[],
						disabled
					).outerHTML}
					${createField(
						FieldTag.textarea,
						null,
						{
							name: 'error',
							key: uniqueId(),
							label: App.text('mailBlockError'),
							value: this.data.error,
							placeholder: App.text('mailBlockDefaultError'),
						},
						[],
						disabled
					).outerHTML}
					${createField(
						FieldTag.textarea,
						null,
						{
							name: 'success',
							key: uniqueId(),
							label: App.text('mailBlockSuccess'),
							value: this.data.success,
							placeholder: App.text('mailBlockDefaultSuccess'),
						},
						[],
						disabled
					).outerHTML}
				</div>
			</div>
		`;

		return this.wrapper;
	}

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	save(): MailBlockData {
		return collectFieldData(this.wrapper) as MailBlockData;
	}
}
