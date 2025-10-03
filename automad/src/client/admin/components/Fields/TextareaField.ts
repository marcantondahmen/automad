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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { create, CSS, debounce, FieldTag, initTabHandler } from '@/admin/core';
import { BaseFieldComponent } from './BaseField';

/**
 * A multiline text field.
 *
 * @extends BaseFieldComponent
 */
class TextareaFieldComponent extends BaseFieldComponent {
	/**
	 * Create an input field.
	 */
	createInput(): void {
		const { name, id, value, placeholder } = this._data;
		const textarea = create(
			'textarea',
			[CSS.input],
			{ id, name, placeholder, wrap: 'off' },
			this
		);

		textarea.textContent = value;

		this.initAutoResize(textarea);
		initTabHandler(textarea);
	}

	/**
	 * Initialize the automatic resizing.
	 *
	 * @param textarea
	 */
	private initAutoResize(textarea: HTMLTextAreaElement) {
		const fit = debounce(() => {
			this.fitContent(textarea);
		}, 50);

		this.listen(textarea, 'keyup focus focusout drop paste', fit);
		this.listen(window, 'resize', fit);

		fit();
	}

	/**
	 * Fit the textarea to the content.
	 *
	 * @param textarea
	 */
	private fitContent(textarea: HTMLTextAreaElement) {
		const clone = create(
			'div',
			[CSS.input],
			{
				style: `position: absolute; height: auto; width: ${textarea.offsetWidth}; white-space: pre-line; opacity: 0;`,
			},
			this
		);

		// Add a random character here to actually make new lines work at the end of the content.
		clone.textContent = textarea.value + '-';
		textarea.style.height = `${clone.offsetHeight + 15}px`;
		clone.remove();
	}
}

customElements.define(FieldTag.textarea, TextareaFieldComponent);
