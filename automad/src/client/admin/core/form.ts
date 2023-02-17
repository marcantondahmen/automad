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
 * Copyright (c) 2022-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	create,
	createField,
	html,
	query,
	queryAll,
	titleCase,
} from '.';
import {
	FieldGroupData,
	FieldGroups,
	InputElement,
	KeyValueMap,
} from '../types';

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
 * Create an ID from a field key.
 *
 * @param key
 * @returns the generated ID
 */
export const createIdFromField = (key: string): string => {
	return `am-field__${key.replace(/(?!^)([A-Z])/g, '-$1').toLowerCase()}`;
};

/**
 * Create a label text from a field key.
 *
 * @param key
 * @returns the generated label
 */
export const createLabelFromField = (key: string): string => {
	return titleCase(key.replace('+', ''))
		.replace('Color ', '')
		.replace('Checkbox ', '');
};

/**
 * Create a group of form fields within a given section element based on a set of keys.
 *
 * @param params
 */
export const fieldGroup = ({
	section,
	fields,
	tooltips,
	shared,
}: FieldGroupData): void => {
	if (Object.values(fields).length == 0) {
		create(
			'am-alert',
			[],
			{
				[Attr.icon]: 'slash-circle',
				[Attr.text]: App.text('sectionHasNoFields'),
			},
			section
		);

		return;
	}

	const prefixMap = {
		'+': 'am-editor',
		checkbox: shared ? 'am-toggle-select' : 'am-toggle',
		color: 'am-color',
		date: 'am-date',
		text: 'am-markdown',
		image: 'am-image-select',
		url: 'am-url',
	} as const;

	Object.keys(fields).forEach((key) => {
		if (!Object.values(App.reservedFields).includes(key)) {
			let fieldType = 'am-textarea';
			let placeholder = '';

			for (const [prefix, value] of Object.entries(prefixMap)) {
				if (key.startsWith(prefix)) {
					fieldType = value;
					break;
				}
			}

			if (shared) {
				placeholder = shared[key];
			}

			createField(
				fieldType,
				section,
				{
					key: key,
					value: fields[key],
					tooltip: tooltips[key],
					name: `data[${key}]`,
					placeholder,
				},
				[],
				{},
				['am-editor', 'am-markdown'].includes(fieldType)
			);
		}
	});
};

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
 * Split the incoming fields into predifend groups.
 *
 * @param fields
 * @returns the field groups
 */
export const prepareFieldGroups = (fields: KeyValueMap): FieldGroups => {
	const groups: FieldGroups = {
		settings: {},
		text: {},
		colors: {},
	};

	Object.keys(fields).forEach((name) => {
		const match = name.match(/^(\+|text|color|.)/);

		switch (match[1]) {
			case '+':
			case 'text':
				groups.text[name] = fields[name];
				break;
			case 'color':
				groups.colors[name] = fields[name];
				break;
			default:
				groups.settings[name] = fields[name];
		}
	});

	return groups;
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
	if (!formData) {
		return;
	}

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
