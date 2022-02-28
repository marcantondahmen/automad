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
	KeyValueMap,
	PageFieldGroups,
	PageMainSettingsData,
	PageSectionCollection,
	PageSectionName,
} from '../../types';
import { FieldComponent } from '../Fields/Field';
import { FormComponent } from './Form';
import { SwitcherSectionComponent } from '../Switcher/SwitcherSection';
import {
	App,
	Binding,
	create,
	createField,
	fire,
	html,
	query,
	Routes,
	setDocumentTitle,
} from '../../core';
import { PageTemplateComponent } from '../Fields/PageTemplate';

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
	 * Wait for pending requests.
	 */
	protected parallel = false;

	/**
	 * Submit form data on changes.
	 */
	protected get auto(): boolean {
		return true;
	}

	/**
	 * Enable self init.
	 */
	protected get initSelf(): boolean {
		return true;
	}

	/**
	 * Initialize the form.
	 */
	protected init(): void {
		this.sections = createSections(this);

		super.init();
	}

	/**
	 * Create the main settings fields.
	 *
	 * @param params
	 * @param params.section - the section element where the fields are created in
	 * @param params.url - the page URL
	 * @param params.slug - the page slug
	 * @param params.pageData - the data that was loaded from the data file
	 * @param params.shared - the shared data object
	 * @param params.reserved - the reserved keys object
	 * @param params.template - the page template path
	 */
	private mainSettings({
		section,
		url,
		slug,
		fields,
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
			label: string = '',
			attributes: KeyValueMap = {}
		): FieldComponent => {
			const data = {
				key,
				value: fields[key],
				name: `data[${key}]`,
				label,
			};

			return createField(fieldType, section, data, [], attributes);
		};

		const titleField = createMainField(
			'am-title',
			App.reservedFields.AM_KEY_TITLE,
			'',
			{
				required: '',
			}
		);

		new Binding('title', titleField.input);

		create(
			'a',
			[],
			{
				href: `${App.baseURL}${fields[App.reservedFields.AM_KEY_URL]}`,
				bind: 'pageUrlWithBase',
				bindto: 'textContent href',
			},
			section
		).innerHTML = fields[App.reservedFields.AM_KEY_URL] || url;

		createMainField(
			'am-checkbox-large',
			App.reservedFields.AM_KEY_PRIVATE,
			App.text('keepPagePrivate')
		);

		const templateField = create(
			'am-page-template',
			[],
			{},
			section
		) as PageTemplateComponent;

		templateField.data = {
			fields,
			template,
			themeKey: App.reservedFields.AM_KEY_THEME,
		};

		createMainField(
			'am-checkbox',
			App.reservedFields.AM_KEY_HIDDEN,
			App.text('hidePage')
		);

		createMainField(
			'am-date',
			App.reservedFields.AM_KEY_DATE,
			App.text('date')
		);

		if (url != '/') {
			const slugField = createField('am-field', section, {
				key: 'slug',
				value: slug,
				name: 'slug',
				label: App.text('pageSlug'),
			});

			new Binding('pageUrl', slugField.input, (value: string) => {
				return `${url.replace(/[^\/]+$/, value)}`;
			});

			new Binding('pageUrlWithBase', slugField.input, (value: string) => {
				return `${App.baseURL}${url.replace(/[^\/]+$/, value)}`;
			});

			new Binding('pageLinkUI', slugField.input, (value: string) => {
				return `${Routes[Routes.page]}?url=${encodeURIComponent(
					url.replace(/[^\/]+$/, value)
				)}`;
			});
		}

		createMainField(
			'am-url',
			App.reservedFields.AM_KEY_URL,
			App.text('redirectPage')
		);

		createMainField(
			'am-page-tags',
			App.reservedFields.AM_KEY_TAGS,
			App.text('pageTags')
		);
	}

	/**
	 * Show alert box for missing page.
	 */
	private pageNotFound() {
		this.innerHTML = html`
			<am-alert
				icon="question-circle"
				text="pageNotFoundError"
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

		if (response.code != 200) {
			this.pageNotFound();

			return;
		}

		if (!response.data) {
			return;
		}

		if (response.data.update) {
			this.updateUI(response.data.update);

			return;
		}

		this.render(response.data);
	}

	/**
	 * Create the actual form fields.
	 *
	 * @param data
	 */
	private render(data: KeyValueMap): void {
		const { url, slug, fields, shared, template } = data;

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
			slug,
			fields,
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

	/**
	 * Handle UI updates.
	 *
	 * @param update
	 */
	private async updateUI(update: KeyValueMap): Promise<void> {
		if (update.slug && update.url && update.origUrl) {
			const slugInput = query('[name="slug"]') as HTMLInputElement;
			const url = new URL(window.location.href);

			url.searchParams.set('url', update.origUrl);
			window.history.replaceState(null, null, url);

			slugInput.value = update.slug;
			fire('input', slugInput);

			await App.updateState();
		}
	}
}

customElements.define('am-page-data', PageDataComponent);
