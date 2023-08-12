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

import { App, createField, CSS, FieldTag, html, uniqueId } from '@/core';
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
		return { value: data.value || '' };
	}

	/**
	 * Sanitize form data before setting the current state.
	 *
	 * @param data
	 * @return the sanitized data
	 */
	protected sanitize(data: ClassTuneData): ClassTuneData {
		return { value: data.value?.replace(/[^\w_\s]+/g, '-').trim() };
	}

	/**
	 * Create the form fields inside of the modal.
	 *
	 * @return the fields wrapper
	 */
	protected createForm(): HTMLElement {
		return createField(FieldTag.input, null, {
			label: this.title,
			value: this.data.value,
			key: uniqueId(),
			name: 'value',
		});
	}

	/**
	 * Render the label.
	 *
	 * @return the rendered label
	 */
	protected renderLabel(): string {
		return this.data.value
			? html`<span class="${CSS.badge}">$${this.data.value}</span>`
			: '';
	}
}
