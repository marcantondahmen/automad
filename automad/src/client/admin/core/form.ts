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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	create,
	createField,
	CSS,
	html,
	listen,
	query,
	queryAll,
	titleCase,
} from '.';
import {
	FieldGroupData,
	FieldGroups,
	FieldSectionCollection,
	InputElement,
	KeyValueMap,
} from '@/admin/types';

/**
 * The tag names enum for fields.
 */
export const enum FieldTag {
	code = 'am-code-field',
	color = 'am-color-field',
	date = 'am-date-field',
	editor = 'am-editor-field',
	email = 'am-email-field',
	feedFieldSelect = 'am-feed-field-select-field',
	image = 'am-image-field',
	input = 'am-input-field',
	mainTheme = 'am-main-theme-field',
	markdown = 'am-markdown-field',
	number = 'am-number-field',
	numberUnit = 'am-number-unit-field',
	select = 'am-select-field',
	pageTags = 'am-page-tags-field',
	pageTemplate = 'am-page-template-field',
	password = 'am-password-field',
	platformSelect = 'am-platform-select-field',
	syntaxSelect = 'am-syntax-theme-select-field',
	textarea = 'am-textarea-field',
	title = 'am-title-field',
	toggle = 'am-toggle-field',
	toggleLarge = 'am-toggle-large-field',
	toggleSelect = 'am-toggle-select-field',
	url = 'am-url-field',
}

/**
 * Input patterns.
 */
export const enum InputPattern {
	username = '^[a-z0-9]([a-z0-9_]|-)+[a-z0-9]$',
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
 * Create all custom CSS and JS fields.
 *
 * @param fields
 * @param sections
 */
export const createCustomizationFields = (
	fields: KeyValueMap,
	sections: FieldSectionCollection,
	shared: KeyValueMap = {}
) => {
	const buildFieldProps = (field: string, label: string) => {
		const key = App.reservedFields[field];

		return {
			key,
			label,
			value: fields[key],
			name: `data[${key}]`,
			placeholder: shared[key] ?? '',
		};
	};

	create('p', [], {}, sections.customizations, App.text('customization'));

	createField(
		FieldTag.code,
		sections.customizations,
		buildFieldProps('CUSTOM_HTML_HEAD', App.text('customHTMLHead'))
	);

	createField(
		FieldTag.code,
		sections.customizations,
		buildFieldProps('CUSTOM_HTML_BODY_END', App.text('customHTMLBodyEnd'))
	);

	createField(
		FieldTag.code,
		sections.customizations,
		buildFieldProps('CUSTOM_JS_HEAD', App.text('customJSHead'))
	);

	createField(
		FieldTag.code,
		sections.customizations,
		buildFieldProps('CUSTOM_JS_BODY_END', App.text('customJSBodyEnd'))
	);

	createField(
		FieldTag.code,
		sections.customizations,
		buildFieldProps('CUSTOM_CSS', App.text('customCSS'))
	);

	create('hr', [], {}, sections.customizations);

	create(
		'p',
		[],
		{},
		sections.customizations,
		App.text('customOpenGraphInfo')
	);

	const ogColors = create(
		'div',
		[CSS.grid, CSS.gridAuto],
		{},
		sections.customizations
	);

	createField(
		FieldTag.color,
		ogColors,
		buildFieldProps(
			'CUSTOM_OPEN_GRAPH_IMAGE_COLOR_TEXT',
			App.text('customOpenGraphImageColorText')
		)
	);

	createField(
		FieldTag.color,
		ogColors,
		buildFieldProps(
			'CUSTOM_OPEN_GRAPH_IMAGE_COLOR_BACKGROUND',
			App.text('customOpenGraphImageColorBackground')
		)
	);

	create('hr', [], {}, sections.customizations);

	create('p', [], {}, sections.customizations, App.text('customConsentInfo'));

	createField(
		FieldTag.input,
		sections.customizations,
		buildFieldProps('CUSTOM_CONSENT_TEXT', App.text('customConsentText'))
	);

	const cookieButtons = create(
		'div',
		[CSS.grid, CSS.gridAuto],
		{ style: '--min: 24rem;' },
		sections.customizations
	);

	createField(
		FieldTag.input,
		cookieButtons,
		buildFieldProps(
			'CUSTOM_CONSENT_ACCEPT',
			App.text('customConsentAccept')
		)
	);

	createField(
		FieldTag.input,
		cookieButtons,
		buildFieldProps(
			'CUSTOM_CONSENT_DECLINE',
			App.text('customConsentDecline')
		)
	);

	createField(
		FieldTag.input,
		cookieButtons,
		buildFieldProps(
			'CUSTOM_CONSENT_REVOKE',
			App.text('customConsentRevoke')
		)
	);

	createField(
		FieldTag.input,
		cookieButtons,
		buildFieldProps(
			'CUSTOM_CONSENT_TOOLTIP',
			App.text('customConsentTooltip')
		)
	);

	createField(
		FieldTag.color,
		sections.customizations,
		buildFieldProps(
			'CUSTOM_CONSENT_COLOR_TEXT',
			App.text('customConsentColorText')
		)
	);

	const cookieColors = create(
		'div',
		[CSS.grid, CSS.gridAuto],
		{ style: '--min: 24rem;' },
		sections.customizations
	);

	createField(
		FieldTag.color,
		cookieColors,
		buildFieldProps(
			'CUSTOM_CONSENT_COLOR_BACKGROUND',
			App.text('customConsentColorBackground')
		)
	);

	createField(
		FieldTag.color,
		cookieColors,
		buildFieldProps(
			'CUSTOM_CONSENT_COLOR_BORDER',
			App.text('customConsentColorBorder')
		)
	);

	createField(
		FieldTag.input,
		sections.customizations,
		buildFieldProps(
			'CUSTOM_CONSENT_PLACEHOLDER_TEXT',
			App.text('customConsentPlaceholderText')
		)
	);

	const cookiePlaceholderColors = create(
		'div',
		[CSS.grid, CSS.gridAuto],
		{},
		sections.customizations
	);

	createField(
		FieldTag.color,
		cookiePlaceholderColors,
		buildFieldProps(
			'CUSTOM_CONSENT_PLACEHOLDER_COLOR_TEXT',
			App.text('customConsentPlaceholderColorText')
		)
	);

	createField(
		FieldTag.color,
		cookiePlaceholderColors,
		buildFieldProps(
			'CUSTOM_CONSENT_PLACEHOLDER_COLOR_BACKGROUND',
			App.text('customConsentPlaceholderColorBackground')
		)
	);

	create(
		'div',
		[CSS.displayLastNone],
		{},
		sections.customizations,
		html`
			<hr />
			<p>${App.text('customizationTemplate')}</p>
		`
	);
};

/**
 * Create an ID from a field key.
 *
 * @param key
 * @returns the generated ID
 */
export const createIdFromField = (key: string): string => {
	if (!key) {
		return;
	}

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
		.replace(/^Color /, '')
		.replace(/^Checkbox /, '')
		.replace(/^Number /, '')
		.replace(/^Select /, '');
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
	themeOptions,
	labels,
	renderEmptyAlert,
	shared,
}: FieldGroupData): void => {
	if (Object.values(fields).length == 0 && renderEmptyAlert) {
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

	const prefixMap = getPrefixMap(!!shared);

	Object.keys(fields).forEach((name) => {
		if (
			Object.values(App.reservedFields).includes(name) ||
			name.startsWith(':')
		) {
			return;
		}

		let fieldType: FieldTag = FieldTag.textarea;
		let placeholder = '';

		for (const [prefix, value] of Object.entries(prefixMap)) {
			if (name.startsWith(prefix)) {
				fieldType = value;
				break;
			}
		}

		if (shared) {
			placeholder = shared[name];
		}

		createField(
			fieldType,
			section,
			{
				key: name,
				value: fields[name],
				tooltip: tooltips[name],
				options: themeOptions[name] ?? null,
				label: labels[name] ?? null,
				name: `data[${name}]`,
				placeholder,
			},
			[],
			{},
			[FieldTag.editor, FieldTag.markdown].includes(fieldType)
		);
	});
};

/**
 * Collect all the form data from a given container.
 *
 * @param container
 * @returns the collected form data
 */
export const collectFieldData = (container: HTMLElement): KeyValueMap => {
	const data: KeyValueMap = {};

	queryAll<HTMLInputElement>(FormDataProviders.selector, container).forEach(
		(input) => {
			const type = input.getAttribute('type');
			const name = input.getAttribute('name');
			const isCheckbox = ['checkbox', 'radio'].includes(type);

			if ((!isCheckbox || input.checked) && name) {
				let value =
					isCheckbox && input.value === '1' ? true : input.value;

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
 * Get the map of prefixes that match the related input type.
 *
 * @param hasSharedDefaults
 * @return the prefix map object
 */
export const getPrefixMap = (hasSharedDefaults: boolean) => {
	return {
		'+': FieldTag.editor,
		checkbox: hasSharedDefaults ? FieldTag.toggleSelect : FieldTag.toggle,
		color: FieldTag.color,
		date: FieldTag.date,
		image: FieldTag.image,
		number: FieldTag.number,
		select: FieldTag.select,
		text: FieldTag.markdown,
		url: FieldTag.url,
	} as const;
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
		customizations: {},
	};

	Object.keys(fields).forEach((name) => {
		const match = name.match(/^(\+|text|color|.)/);

		switch (match[1]) {
			case '+':
			case 'text':
				groups.text[name] = fields[name];
				break;
			case 'color':
				groups.customizations[name] = fields[name];
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
