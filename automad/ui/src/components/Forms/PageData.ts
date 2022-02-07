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

import {
	FieldGroupData,
	FieldInitData,
	KeyValueMap,
	PageFieldGroups,
	PageMainSettingsData,
	PageSectionCollection,
	PageSectionName,
} from '../../types';
import { FieldComponent } from '../Fields/Field';
import { FormComponent } from './Form';
import { SwitcherSectionComponent } from '../SwitcherSection';
import {
	App,
	classes,
	create,
	html,
	keyCombo,
	query,
	setDocumentTitle,
} from '../../core';
import { PageTemplateComponent } from '../Fields/PageTemplate';

/**
 * Create a form field and set its data.
 *
 * @param fieldType the field type name
 * @param section the section node where the field is created in
 * @param data the field data object
 * @param cls the array with optional class name
 * @returns the generated field
 */
const createField = (
	fieldType: string,
	section: HTMLElement,
	data: FieldInitData,
	cls: string[] = []
): FieldComponent => {
	const field = create(fieldType, cls, {}, section);

	field.data = data;

	return field;
};

/**
 * Create a group of form fields within a given section element based on a set of keys.
 *
 * @param params
 * @param params.section - the section node where the field is created in
 * @param params.keys - the array of variable keys for the field group
 * @param params.pageData - the data object that was loaded from the page's data file
 * @param params.tooltips - the field tooltips
 */
const fieldGroup = ({ section, fields, tooltips }: FieldGroupData): void => {
	const prefixMap = {
		'+': 'am-editor',
		checkbox: 'am-checkbox-page',
		color: 'am-color',
		date: 'am-date',
		text: 'am-markdown',
		image: 'am-image',
		url: 'am-url',
	};

	Object.keys(fields).forEach((key) => {
		if (!Object.values(App.reservedFields).includes(key)) {
			let fieldType = 'am-textarea';

			for (const [prefix, value] of Object.entries(prefixMap)) {
				if (key.startsWith(prefix)) {
					fieldType = value;
					break;
				}
			}

			createField(fieldType, section, {
				key: key,
				value: fields[key],
				tooltip: tooltips[key],
				name: `data[${key}]`,
			});
		}
	});
};

/**
 * Create switcher sections for the different kind of variable fields.
 *
 * @param form - the main page data form that serves as wrapper
 * @returns the switcher section collection
 */
const createSections = (form: PageDataComponent): PageSectionCollection => {
	const createSection = (key: string): SwitcherSectionComponent => {
		return create('am-switcher-section', [], { name: content[key] }, form);
	};

	const content = App.sections.content;

	const sections: PageSectionCollection = {
		settings: createSection('settings'),
		text: createSection('text'),
		colors: createSection('colors'),
	};

	return sections;
};

/**
 * Split the incoming fields into predifend groups.
 *
 * @param fields
 * @returns the field groups
 */
const prepareFieldGroups = (fields: KeyValueMap): PageFieldGroups => {
	const groups: PageFieldGroups = {
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
 * A page data form element.
 * The PageDataComponent class doesn't need the watch and init properties
 * as this is anyways the intended behavior.
 *
 * @example
 * <am-page-data api="Page/data"></am-page-data>
 * <am-submit form="Page/data">Submit</am-submit>
 *
 * @extends FormComponent
 */
export class PageDataComponent extends FormComponent {
	/**
	 * The section collection object.
	 */
	private sections: PageSectionCollection;

	/**
	 * Enable self init.
	 */
	protected get initSelf(): boolean {
		return true;
	}

	/**
	 * Initialize the form.
	 */
	protected async init(): Promise<void> {
		this.sections = createSections(this);

		super.init();
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		keyCombo('s', () => {
			if (this.hasUnsavedChanges) {
				this.submit();
			}
		});
	}

	/**
	 * Create the main settings fields.
	 *
	 * @param params
	 * @param params.section - the section element where the fields are created in
	 * @param params.url - the page URL
	 * @param params.prefix - the directory prefix
	 * @param params.slug - the page slug
	 * @param params.pageData - the data that was loaded from the data file
	 * @param params.shared - the shared data object
	 * @param params.reserved - the reserved keys object
	 * @param params.template - the page template path
	 */
	private mainSettings({
		section,
		url,
		prefix,
		slug,
		fields,
		shared,
		template,
	}: PageMainSettingsData): void {
		/**
		 * Create a field for one of the main settings.
		 *
		 * @param fieldType
		 * @param key
		 * @param [label]
		 * @returns the generated field
		 */
		const createMainField = (
			fieldType: string,
			key: string,
			label: string = ''
		): FieldComponent => {
			const data = {
				key,
				value: fields[key],
				name: `data[${key}]`,
				label,
			};

			return createField(fieldType, section, data, []);
		};

		const title = createMainField(
			'am-field',
			App.reservedFields.AM_KEY_TITLE
		);

		query('input', title).classList.add(classes.inputTitle);

		create(
			'a',
			[],
			{ href: `${App.baseURL}${fields[App.reservedFields.AM_KEY_URL]}` },
			section
		).innerHTML = fields[App.reservedFields.AM_KEY_URL] || url;

		createMainField('am-checkbox-large', App.reservedFields.AM_KEY_PRIVATE);

		const templateField = create(
			'am-page-template',
			[],
			{},
			section
		) as PageTemplateComponent;

		templateField.data = {
			fields,
			shared,
			template,
			themeKey: App.reservedFields.AM_KEY_THEME,
		};

		createMainField('am-checkbox', App.reservedFields.AM_KEY_HIDDEN);

		if (url != '/') {
			createField('am-field', section, {
				key: 'prefix',
				value: prefix,
				name: 'prefix',
				label: App.text('page_prefix'),
			});

			createField('am-field', section, {
				key: 'slug',
				value: slug,
				name: 'slug',
				label: App.text('page_slug'),
			});
		}

		createMainField(
			'am-url',
			App.reservedFields.AM_KEY_URL,
			App.text('page_redirect')
		);

		createMainField(
			'am-page-tags',
			App.reservedFields.AM_KEY_TAGS,
			App.text('page_tags')
		);
	}

	/**
	 * Show alert box for missing page.
	 */
	private pageNotFound() {
		this.innerHTML = html`
			<am-alert
				icon="question-circle"
				text="error_page_not_found"
				type="danger"
			></am-alert>
		`;
	}

	/**
	 * Create the form after the response was received successfully.
	 *
	 * @param response - the response data
	 */
	protected processResponse(response: KeyValueMap): void {
		super.processResponse(response);

		if (typeof response.data == 'undefined') {
			this.pageNotFound();

			return;
		}

		this.watch();

		const { url, prefix, slug, fields, shared, template } = response.data;

		const themeKey = App.reservedFields.AM_KEY_THEME;
		const themes = App.themes;

		setDocumentTitle(fields.title);

		let tooltips = {};

		if (fields[themeKey]) {
			tooltips = themes[fields[themeKey]].tooltips;
		} else {
			tooltips = themes[shared[themeKey]].tooltips;
		}

		const fieldGroups = prepareFieldGroups(fields);

		this.mainSettings({
			section: this.sections.settings,
			url,
			prefix,
			slug,
			fields,
			shared,
			template,
		});

		Object.keys(this.sections).forEach((item: PageSectionName) => {
			fieldGroup({
				section: this.sections[item],
				fields: fieldGroups[item],
				tooltips,
			});
		});
	}
}

customElements.define('am-page-data', PageDataComponent);
