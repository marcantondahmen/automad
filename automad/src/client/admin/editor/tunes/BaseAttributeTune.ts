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

import {
	collectFieldData,
	create,
	createField,
	CSS,
	FieldTag,
	html,
	query,
	uniqueId,
} from '@/admin/core';
import { AttributeTuneData } from '@/admin/types';
import { BaseModalTune } from './BaseModalTune';

export abstract class BaseAttributeTune extends BaseModalTune<AttributeTuneData> {
	/**
	 * The attribute name.
	 */
	protected abstract getAttrName(): string;

	/**
	 * Render the displayed value.
	 */
	protected abstract renderAttr(): string;

	/**
	 * The validation pattern for the input field.
	 */
	protected abstract getInputPattern(): string;

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: AttributeTuneData): AttributeTuneData {
		return data ?? '';
	}

	/**
	 * Sanitize form data before setting the current state.
	 *
	 * @param data
	 * @return the sanitized data
	 */
	protected abstract sanitize(data: AttributeTuneData): AttributeTuneData;

	/**
	 * Extract the class from the form.
	 *
	 * @param container
	 * @return the class
	 */
	protected getFormData(container: HTMLElement): string {
		const data = collectFieldData(container);

		return data[this.getAttrName()];
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
				name: this.getAttrName(),
			},
			[],
			{
				pattern: this.getInputPattern(),
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
			? html`
					<span class="${CSS.badge}">
						<span>$${this.data}</span>
					</span>
				`
			: '';
	}

	/**
	 * Apply tune to block content element.
	 *
	 * @param the block content element
	 * @return the element
	 */
	protected wrap(blockElement: HTMLElement): HTMLElement {
		if (!this.data.length) {
			return blockElement;
		}

		const badges =
			query('.__badges', blockElement) ??
			create(
				'span',
				['__badges', CSS.editorTunesAttributeBadges],
				{},
				blockElement,
				'',
				true
			);

		const badgeContainer =
			query(`.__${this.getAttrName()}`, badges) ??
			create('span', [`__${this.getAttrName()}`], {}, badges, '', true);

		badgeContainer.innerHTML = this.data
			? html`
					<span class="${CSS.badge}">
						<span>${this.renderAttr()}</span>
					</span>
				`
			: '';

		return blockElement;
	}
}
