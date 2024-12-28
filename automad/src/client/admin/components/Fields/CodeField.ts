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
 * Copyright (c) 2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { create, CSS, FieldTag, FormDataProviders } from '@/admin/core';
import { CodeEditor } from '@/admin/core/code';
import { UndoValue } from '@/admin/types';
import { BaseFieldComponent } from './BaseField';

/**
 * A code field with a label.
 *
 * @extends BaseFieldComponent
 */
class CodeFieldComponent extends BaseFieldComponent {
	/**
	 * The editor value that serves a input value for the parent form.
	 */
	value: string = '';

	/**
	 * The editor component.
	 */
	private editor: CodeEditor;

	/**
	 * Get the language based on the field name.
	 *
	 * @param name
	 */
	private getLanguageFromName = (name: string) => {
		const sanitized = name.replace(/\W+/g, '');

		if (sanitized.match(/html/i)) {
			return 'html';
		}

		if (sanitized.match(/js/i)) {
			return 'javascript';
		}

		return 'css';
	};

	/**
	 * Render the field.
	 */
	protected createInput(): void {
		const { name, id, value, placeholder } = this._data;

		this.setAttribute('name', name);
		this.value = value as string;

		this.editor = new CodeEditor({
			element: create('div', [CSS.codeflask], { id }, this),
			code: value as string,
			language: this.getLanguageFromName(name),
			onChange: (code) => (this.value = code),
			placeholder: placeholder as string,
		});
	}

	/**
	 * Return the field that is observed for changes.
	 *
	 * @return the input field
	 */
	getValueProvider(): HTMLElement {
		return this;
	}

	/**
	 * A function that can be used to mutate the field value.
	 *
	 * @param value
	 */
	async mutate(value: UndoValue): Promise<void> {
		this.editor.codeFlask.updateCode(value);
		this.value = value;
	}

	/**
	 * Query the current field value.
	 *
	 * @return the current value
	 */
	query() {
		return this.value;
	}
}

FormDataProviders.add(FieldTag.code);
customElements.define(FieldTag.code, CodeFieldComponent);
