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

/**
 * The array of valied field selectors that are used to collect the form data.
 */
const formDataControls: string[] = ['input', 'textarea', 'select', 'am-editor'];

/**
 * Collect all the form data to be submitted. Note that excludes all values of unchecked checkboxes and radios.
 *
 * @param container
 * @returns the form data object
 */
export const getFormData = (container: HTMLElement): KeyValueMap => {
	const data: KeyValueMap = {};

	queryAll(formDataControls.join(','), container).filter(
		(input: HTMLInputElement) => {
			const type = input.getAttribute('type');
			const name = input.getAttribute('name');
			const isCheckbox = ['checkbox', 'radio'].includes(type);

			if ((!isCheckbox || input.checked) && name) {
				data[name] = input.value.trim();
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
