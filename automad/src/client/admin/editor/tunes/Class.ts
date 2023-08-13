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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	collectFieldData,
	createField,
	CSS,
	FieldTag,
	html,
	uniqueId,
} from '@/core';
import { ClassTuneData } from '@/types';
import { BaseModalTune } from './BaseModalTune';

export class ClassTune extends BaseModalTune<ClassTuneData> {
	/**
	 * The tune title.
	 */
	get title() {
		return App.text('className');
	}

	/**
	 * The tune icon.
	 */
	get icon() {
		return '<i class="bi bi-asterisk"></i>';
	}

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: ClassTuneData): ClassTuneData {
		return data ?? '';
	}

	/**
	 * Sanitize form data before setting the current state.
	 *
	 * @param data
	 * @return the sanitized data
	 */
	protected sanitize(data: ClassTuneData): ClassTuneData {
		return (data || '').replace(/[^\w_\s]+/g, '-').trim();
	}

	/**
	 * Extract the id from the form.
	 *
	 * @param modal
	 * @return the id
	 */
	protected getFormData(modal: HTMLElement): string {
		const { className } = collectFieldData(modal);

		return className;
	}

	/**
	 * Create the form fields inside of the modal.
	 *
	 * @return the fields wrapper
	 */
	protected createForm(): HTMLElement {
		return createField(FieldTag.input, null, {
			label: this.title,
			value: this.data,
			key: uniqueId(),
			name: 'className',
		});
	}

	/**
	 * Render the label.
	 *
	 * @return the rendered label
	 */
	protected renderLabel(): string {
		return this.data
			? html`<span class="${CSS.badge}">$${this.data}</span>`
			: '';
	}
}
