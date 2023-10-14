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
	listen,
	query,
	queryAll,
	titleCase,
} from '.';
import {
	FieldGroupData,
	FieldGroups,
	InputElement,
	KeyValueMap,
} from '@/types';

/**
 * The tag names enum for fields.
 */
export const enum FieldTag {
	editor = 'am-editor',
	email = 'am-email',
	feedFieldSelect = 'am-feed-field-select',
	input = 'am-input',
	mainTheme = 'am-main-theme',
	pageTags = 'am-page-tags',
	pageTemplate = 'am-page-template',
	password = 'am-password',
	number = 'am-number',
	numberUnit = 'am-number-unit',
	toggle = 'am-toggle',
	toggleSelect = 'am-toggle-select',
	toggleLarge = 'am-toggle-large',
	color = 'am-color',
	date = 'am-date',
	markdown = 'am-markdown',
	imageSelect = 'am-image-select',
	url = 'am-url',
	textarea = 'am-textarea',
	title = 'am-title',
}

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
	 * Note that the returned selector is not only a joined list
	 * of input selectors but also prevents collecting field data from
	 * nested editors.
	 *
	 * @static
	 */
	static get selector() {
		const controls = this._controls.map((control) => {
			return `${control}:not(:scope ${FieldTag.editor} ${control})`;
		});

		return controls.join(', ');
	}

	/**
	 * Add a new type of form control to the list of controls that generate form data.
	 *
	 * @param name
	 * @static
	 */
	static add(name: string): void {
		if (!this._controls.includes(name)) {
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
	return `am-field__${key.replace(/\s+/g, '_')}`;
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
		'+': FieldTag.editor,
		checkbox: shared ? FieldTag.toggleSelect : FieldTag.toggle,
		color: FieldTag.color,
		date: FieldTag.date,
		text: FieldTag.markdown,
		image: FieldTag.imageSelect,
		url: FieldTag.url,
	} as const;

	Object.keys(fields).forEach((key) => {
		if (!Object.values(App.reservedFields).includes(key)) {
			let fieldType: FieldTag = FieldTag.textarea;
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
				[FieldTag.editor, FieldTag.markdown].includes(fieldType)
			);
		}
	});
};

/**
 * Collect all the form data from a given container.
 * Note that excludes all values of unchecked checkboxes and radios.
 *
 * @param container
 * @returns the collected form data
 */
export const collectFieldData = (container: HTMLElement): KeyValueMap => {
	const data: KeyValueMap = {};

	queryAll<HTMLInputElement>(FormDataProviders.selector, container).filter(
		(input) => {
			const type = input.getAttribute('type');
			const name = input.getAttribute('name');
			const isCheckbox = ['checkbox', 'radio'].includes(type);

			if ((!isCheckbox || input.checked) && name) {
				let value = type === 'checkbox' ? true : input.value;

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

	queryAll<HTMLInputElement>('[type="radio"], [type="input"]').forEach(
		(input) => {
			input.checked = false;
		}
	);

	Object.keys(formData).forEach((name) => {
		const control = query<InputElement>(`[name="${name}"]`, container);

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
 * Initialize handling of the tab key.
 *
 * @param textarea
 */
export const initTabHandler = (textarea: HTMLTextAreaElement) => {
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
};
