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
	collectFieldData,
	create,
	createField,
	CSS,
	FieldTag,
	html,
	query,
	uniqueId,
} from '@/admin/core';
import { ClassTuneData } from '@/admin/types';
import { BaseModalTune } from './BaseModalTune';

export class ClassTune extends BaseModalTune<ClassTuneData> {
	/**
	 * The sort order for this tune.
	 */
	public sort: number = 202;

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
	 * Extract the class from the form.
	 *
	 * @param container
	 * @return the class
	 */
	protected getFormData(container: HTMLElement): string {
		const { className } = collectFieldData(container);

		return className;
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
				name: 'className',
			},
			[],
			{
				pattern: '[\\w\\-\\s_:]*',
			}
		);
	}

	/**
	 * Render the label.
	 *
	 * @return the rendered label
	 */
	protected renderLabel(): string {
		return this.data
			? html`<span class="${CSS.badge}">$${this.data}</span> `
			: '';
	}

	/**
	 * Apply tune to block content element.
	 *
	 * @param the block content element
	 * @return the element
	 */
	protected wrap(blockElement: HTMLElement): HTMLElement {
		const badgeContainer =
			query('.__class', blockElement) ??
			create('span', ['__class'], {}, blockElement, '', true);

		badgeContainer.innerHTML = this.data
			? html`
					<span class="${CSS.badge}">
						.${this.data.replace(/\s+/g, '.')}
					</span>
				`
			: '';

		return blockElement;
	}
}
