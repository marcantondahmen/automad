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

import { classes, keyCombo, query } from '../../core/utils';
import { create } from '../../core/create';
import { KeyValueMap } from '../../core/types';
import { FieldComponent } from '../Fields/Field';
import { FormComponent } from './Form';
import { SwitcherSectionComponent } from '../Switcher';
import { App } from '../../core/app';

type SwitcherSectionName = 'settings' | 'text' | 'colors';

type SwitcherSectionCollection = {
	[name in SwitcherSectionName]: SwitcherSectionComponent;
};

interface FieldGroupData {
	section: SwitcherSectionComponent;
	keys: string[];
	pageData: KeyValueMap;
	tooltips: KeyValueMap;
	removable: boolean;
}

interface MainSettingsData {
	section: SwitcherSectionComponent;
	url: string;
	prefix: string;
	slug: string;
	pageData: KeyValueMap;
	shared: KeyValueMap;
	reserved: KeyValueMap;
	template: string;
}

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
	data: KeyValueMap,
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
 * @param params.removable - true if the field should be removable
 */
const fieldGroup = ({
	section,
	keys,
	pageData,
	tooltips,
	removable,
}: FieldGroupData): void => {
	const prefixMap = {
		'+': 'am-editor',
		checkbox: 'am-checkbox-page',
		color: 'am-color',
		date: 'am-date',
		text: 'am-markdown',
		image: 'am-image',
		url: 'am-url',
	};

	keys.forEach((key) => {
		let fieldType = 'am-textarea';

		for (const [prefix, value] of Object.entries(prefixMap)) {
			if (key.startsWith(prefix)) {
				fieldType = value;
				break;
			}
		}

		createField(fieldType, section, {
			key: key,
			value: pageData[key],
			tooltip: tooltips[key],
			name: `data[${key}]`,
			removable,
		});
	});
};

/**
 * Create switcher sections for the different kind of variable fields.
 *
 * @param form - the main page data form that serves as wrapper
 * @returns the switcher section collection
 */
const createSections = (form: PageDataComponent): SwitcherSectionCollection => {
	const createSection = (key: string): SwitcherSectionComponent => {
		return create(
			'am-switcher-section',
			[classes.spinner],
			{ name: content[key] },
			form
		);
	};

	const content = App.sections.content;

	const sections: SwitcherSectionCollection = {
		settings: createSection('settings'),
		text: createSection('text'),
		colors: createSection('colors'),
	};

	return sections;
};

/**
 * A page data form element.
 * The PageDataComponent class doesn't need the watch and init properties
 * as this is anyways the intended behavior.
 *
 * @example
 * <am-page-data controller="PageController::data"></am-page-data>
 * <am-submit form="PageController::data">Submit</am-submit>
 *
 * @extends FormComponent
 */
export class PageDataComponent extends FormComponent {
	/**
	 * The section collection object.
	 */
	private sections: SwitcherSectionCollection;

	/**
	 * Enable watching.
	 */
	protected watchChanges: boolean = true;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		this.sections = createSections(this);

		this.submit();

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
		pageData,
		shared,
		reserved,
		template,
	}: MainSettingsData): void {
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
				value: pageData[key],
				name: `data[${key}]`,
				label,
			};

			return createField(fieldType, section, data, []);
		};

		const title = createMainField('am-field', reserved['AM_KEY_TITLE']);

		query('input', title).classList.add(classes.inputTitle);

		create(
			'a',
			[],
			{ href: `${App.baseURL}${pageData[reserved['AM_KEY_URL']]}` },
			section
		).innerHTML = pageData[reserved['AM_KEY_URL']] || url;

		createMainField('am-checkbox-large', reserved['AM_KEY_PRIVATE']);

		createField(
			'am-page-template',
			section,
			{
				pageData,
				shared,
				template,
				themeKey: reserved['AM_KEY_THEME'],
			},
			[]
		);

		createMainField('am-checkbox', reserved['AM_KEY_HIDDEN']);

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
			reserved['AM_KEY_URL'],
			App.text('page_redirect')
		);

		createMainField(
			'am-page-tags',
			reserved['AM_KEY_TAGS'],
			App.text('page_tags')
		);
	}

	/**
	 * Show alert box for missing page.
	 */
	private pageNotFound() {
		this.innerHTML = `
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
		if (typeof response.data == 'undefined') {
			this.pageNotFound();

			return;
		}

		this.watch();

		Object.values(this.sections).forEach((section) => {
			section.classList.remove(classes.spinner);
		});

		const {
			url,
			prefix,
			slug,
			pageData,
			shared,
			template,
			keys,
			keysUnused,
		} = response.data;

		const themeKey = keys.reserved['AM_KEY_THEME'];
		const themes = App.themes;
		const reserved = keys.reserved;

		let tooltips = {};

		try {
			tooltips = themes[pageData[themeKey]].tooltips;
		} catch (e) {
			try {
				tooltips = themes[shared[themeKey]].tooltips;
			} catch (e) {}
		}

		this.mainSettings({
			section: this.sections.settings,
			url,
			prefix,
			slug,
			pageData,
			shared,
			reserved,
			template,
		});

		Object.keys(this.sections).forEach((item: SwitcherSectionName) => {
			fieldGroup({
				section: this.sections[item],
				keys: keys[item],
				pageData,
				tooltips,
				removable: false,
			});

			fieldGroup({
				section: this.sections[item],
				keys: keysUnused[item],
				pageData,
				tooltips,
				removable: true,
			});
		});
	}
}

customElements.define('am-page-data', PageDataComponent);
