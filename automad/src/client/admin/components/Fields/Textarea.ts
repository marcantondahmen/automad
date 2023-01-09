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
 * Copyright (c) 2021-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { create, CSS, debounce, listen } from '../../core';
import { BaseFieldComponent } from './BaseField';

/**
 * A multiline text field.
 *
 * @extends BaseFieldComponent
 */
class TextareaComponent extends BaseFieldComponent {
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
		this.initTabHandler(textarea);
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

		listen(textarea, 'keyup focus focusout drop paste', fit);
		this.listeners.push(listen(window, 'resize', fit));

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

	/**
	 * Initialize handling of the tab key.
	 * @param textarea
	 */
	private initTabHandler(textarea: HTMLTextAreaElement) {
		listen(textarea, 'keydown', (event: KeyboardEvent) => {
			if (event.keyCode === 9) {
				event.preventDefault();
				event.stopPropagation();

				const selectionStart = textarea.selectionStart;
				const selectionEnd = textarea.selectionEnd;
				const value = textarea.value;

				textarea.value = `${value.substring(
					0,
					selectionStart
				)}\t${value.substring(selectionEnd)}`;

				textarea.selectionStart = selectionStart + 1;
				textarea.selectionEnd = selectionStart + 1;
			}
		});
	}
}

customElements.define('am-textarea', TextareaComponent);
