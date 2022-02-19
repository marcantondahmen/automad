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

import { classes, query, queryAll } from '.';
import { InputElement, KeyValueMap } from '../types';

/**
 * The array of valied field selectors that are used to collect the form data.
 */
const formDataControls: string[] = [
	'input[type="text"]',
	'input[type="password"]',
	'input[type="datetime-local"]',
	'input[type="hidden"]',
	'input[type="checkbox"]:checked',
	'input[type="radio"]:checked',
	'textarea',
	'select',
	'am-editor',
];

/**
 * Collect all the form data to be submitted. Note that excludes all values of unchecked checkboxes and radios.
 *
 * @param container
 * @returns the form data object
 */
export const getFormData = (container: HTMLElement): KeyValueMap => {
	const data: KeyValueMap = {};

	queryAll(formDataControls.join(','), container).forEach(
		(field: InputElement) => {
			const name = field.getAttribute('name');
			const value = field.value;

			if (name) {
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
		const control = query(
			`[name="${name}"]`,
			container
		) as HTMLInputElement;

		if (control) {
			const type = control.getAttribute('type');

			if (['checkbox', 'radio'].includes(type)) {
				control.checked = true;
			} else {
				control.value = formData[name];
			}
		}
	});
};

/**
 * Toggle the field status indicator.
 *
 * @param element
 * @param changed
 */
export const updateFieldStatus = (
	element: HTMLElement,
	changed: boolean
): void => {
	const field = element.closest(`.${classes.field}`);

	if (field) {
		field.classList.toggle(classes.fieldChanged, changed);
	}
};

/**
 * Reset the field status in a given container.
 *
 * @param container
 */
export const resetFieldStatus = (container: HTMLElement): void => {
	setTimeout(() => {
		queryAll(`.${classes.input}`, container).forEach(
			(input: InputElement) => {
				updateFieldStatus(input, false);
			}
		);
	}, 200);
};
