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
	collectFieldData,
	createField,
	CSS,
	FieldTag,
	html,
	uniqueId,
} from '@/core';
import { IdTuneData } from '@/types';
import { BaseModalTune } from './BaseModalTune';

export class IdTune extends BaseModalTune<IdTuneData> {
	/**
	 * The tune title.
	 */
	get title() {
		return 'ID';
	}

	/**
	 * The tune icon.
	 */
	get icon() {
		return '<span style="font-size: 1.2rem">#</span>';
	}

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: IdTuneData): IdTuneData {
		return data ?? '';
	}

	/**
	 * Sanitize form data before setting the current state.
	 *
	 * @param data
	 * @return the sanitized data
	 */
	protected sanitize(data: IdTuneData): IdTuneData {
		return (data || '').replace(/[^\w_]+/g, '-').trim();
	}

	/**
	 * Extract the id from the form.
	 *
	 * @param modal
	 * @return the id
	 */
	protected getFormData(modal: HTMLElement): string {
		const { id } = collectFieldData(modal);

		return id;
	}

	/**
	 * Create the form fields inside of the modal.
	 *
	 * @return the fields wrapper
	 */
	protected createForm(): HTMLElement {
		return createField(
			FieldTag.input,
			null,
			{
				label: this.title,
				value: this.data,
				key: uniqueId(),
				name: 'id',
			},
			[],
			{ pattern: '[\\w\\-_]*' }
		);
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
