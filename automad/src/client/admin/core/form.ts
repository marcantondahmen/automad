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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { query, queryAll } from '.';
import { InputElement, KeyValueMap } from '../types';
import { html } from './utils';

/**
 * A class to register elements to be used to generate form data.
 */
export class FormDataProviders {
	/**
	 * The list of controls that generate form data.
	 *
	 * @static
	 */
	private static _controls: string[] = ['input', 'textarea', 'select'];

	/**
	 * Get the registered controls as selector.
	 *
	 * @static
	 */
	static get selector() {
		return this._controls.join(', ');
	}

	/**
	 * Add a new type of form control to the list of controls that generate form data.
	 *
	 * @param name
	 * @static
	 */
	static add(name: string): void {
		if (!this._controls.includes('name')) {
			this._controls.push(name);
		}
	}
}

/**
 * Collect all the form data to be submitted. Note that excludes all values of unchecked checkboxes and radios.
 *
 * @param container
 * @returns the form data object
 */
export const getFormData = (container: HTMLElement): KeyValueMap => {
	const data: KeyValueMap = {};

	queryAll(FormDataProviders.selector, container).filter(
		(input: HTMLInputElement) => {
			const type = input.getAttribute('type');
			const name = input.getAttribute('name');
			const isCheckbox = ['checkbox', 'radio'].includes(type);

			if ((!isCheckbox || input.checked) && name) {
				let value = input.value;

				if (typeof value == 'string') {
					value = value.trim();
				}

				data[name] = value;
			}
		}
	);

	return data;
};

/**
 * Set the values for all form controls contained in container that also exist in the form data object.
 *
 * @param formData
 * @param container
 */
export const setFormData = (
	formData: KeyValueMap,
	container: HTMLElement
): void => {
	queryAll('[type="radio"], [type="input"]').forEach(
		(input: HTMLInputElement) => {
			input.checked = false;
		}
	);

	Object.keys(formData).forEach((name) => {
		const control = query(`[name="${name}"]`, container) as InputElement;

		if (control) {
			const type = control.getAttribute('type');

			if (['checkbox', 'radio'].includes(type)) {
				(control as HTMLInputElement).checked = true;
			} else {
				control.value = formData[name];
			}
		}
	});
};

/**
 * Render the select options markup.
 *
 * @param options
 * @returns the rendered options
 */
export const renderOptions = (options: KeyValueMap[]): string => {
	let output = '';

	options.forEach((option) => {
		output += html`
			<option value="${option.value}">${option.text}</option>
		`;
	});

	return output;
};
