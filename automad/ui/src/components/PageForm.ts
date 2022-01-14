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

import Tagify from '@yaireo/tagify';
import {
	classes,
	getBaseURL,
	getSwitcherSections,
	getTags,
	getThemes,
	query,
	text,
	titleCase,
} from '../utils/core';
import { create } from '../utils/create';
import { KeyValueMap, Theme } from '../utils/types';
import { BaseComponent } from './Base';
import { FieldComponent } from './Field';
import { FormComponent } from './Form';
import { SwitcherSectionComponent } from './Switcher';

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

interface ThemeStatus {
	buttonLabel: string;
	buttonClass: string;
	buttonIcon: string;
	selectedTemplate: string;
	mainTheme: Theme;
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

interface TemplateFieldData {
	pageData: KeyValueMap;
	shared: KeyValueMap;
	template: string;
	themeKey: string;
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
		'+': 'am-field-editor',
		checkbox: 'am-field-checkbox-page',
		color: 'am-field-color',
		date: 'am-field-date',
		text: 'am-field-markdown',
		image: 'am-field-image',
		url: 'am-field-url',
	};

	keys.forEach((key) => {
		let fieldType = 'am-field-textarea';

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
 * Beautify a template path to be used as name.
 *
 * @param template - the path of a given template
 * @param [themeName] - an optional theme name
 * @returns the beautified template name
 */
const templateName = (template: string, themeName: string = ''): string => {
	const templateName = template
		.split('/')
		.reverse()[0]
		.replace(/\.php$/g, '');

	return titleCase([themeName, templateName].join('/'));
};

/**
 * Simplify a template/theme path combination to represent a theme variable value.
 *
 * @param template - a template path
 * @param [path] - an optional path
 * @returns the template path
 */
const templatePath = (template: string, path: string = ''): string => {
	const templateName = template
		.split('/')
		.reverse()[0]
		.replace(/\.php$/g, '');

	return [path, templateName].filter((item) => item.length).join('/');
};

/**
 * Get all status info about the selected template.
 *
 * @param params
 * @param params.pageData - the data object that was loaded from the page's data file
 * @param params.shared - the shared data object
 * @param params.template - a template path
 * @param params.themeKey - the field name for themes
 * @returns the UI items the represent a theme status
 */
const themeStatus = ({
	pageData,
	shared,
	template,
	themeKey,
}: TemplateFieldData): ThemeStatus => {
	const themes = getThemes();
	let mainTheme = themes[shared[themeKey]];

	if (typeof mainTheme == 'undefined') {
		mainTheme = Object.values(themes)[0];
	}

	let templateExists = false;
	let appliedTheme = mainTheme;
	let selectedTemplate = templatePath(template);
	let buttonLabel = titleCase(selectedTemplate);
	let buttonIcon = 'file-earmark-code';
	let buttonClass = 'success';

	if (typeof themes[pageData[themeKey]] != 'undefined') {
		appliedTheme = themes[pageData[themeKey]];
		buttonLabel = titleCase(`${appliedTheme.name}/${selectedTemplate}`);
		selectedTemplate = templatePath(template, appliedTheme.path);
	}

	templateExists = appliedTheme.templates.indexOf(template) !== -1;

	if (!templateExists) {
		buttonIcon = 'question-circle';
		buttonClass = 'danger';
	}

	return {
		buttonLabel,
		buttonClass,
		buttonIcon,
		selectedTemplate,
		mainTheme,
	};
};

/**
 * Create switcher sections for the different kind of variable fields.
 *
 * @param form - the main page data form that serves as wrapper
 * @returns the switcher section collection
 */
const createSections = (form: PageFormComponent): SwitcherSectionCollection => {
	const createSection = (key: string): SwitcherSectionComponent => {
		return create('am-switcher-section', [], { name: content[key] }, form);
	};

	const content = getSwitcherSections().content;

	const sections: SwitcherSectionCollection = {
		settings: createSection('settings'),
		text: createSection('text'),
		colors: createSection('colors'),
	};

	return sections;
};

/**
 * A page data form element.
 * The FormPageComponent class doesn't need the watch and init properties
 * as this is anyways the intended behavior.
 *
 * @example
 * <am-form-page controller="FormPageComponent::data" page="/url"></am-form-page>
 * <am-form-submit form="FormPageComponent::data">Submit</am-form-submit>
 *
 * @extends FormComponent
 */
class PageFormComponent extends FormComponent {
	/**
	 * The section collection object.
	 */
	sections: SwitcherSectionCollection;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		this.sections = createSections(this);

		this.submit();
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
			{ href: `${getBaseURL()}${pageData[reserved['AM_KEY_URL']]}` },
			section
		).innerHTML = pageData[reserved['AM_KEY_URL']] || url;

		createMainField('am-field-checkbox-large', reserved['AM_KEY_PRIVATE']);

		createField(
			'am-form-page-field-template',
			section,
			{
				pageData,
				shared,
				template,
				themeKey: reserved['AM_KEY_THEME'],
			},
			[]
		);

		createMainField('am-field-checkbox', reserved['AM_KEY_HIDDEN']);

		if (url != '/') {
			createField('am-field', section, {
				key: 'prefix',
				value: prefix,
				name: 'prefix',
				label: text('page_prefix'),
			});

			createField('am-field', section, {
				key: 'slug',
				value: slug,
				name: 'slug',
				label: text('page_slug'),
			});
		}

		createMainField(
			'am-field-url',
			reserved['AM_KEY_URL'],
			text('page_redirect')
		);

		createMainField(
			'am-form-page-field-tags',
			reserved['AM_KEY_TAGS'],
			text('page_tags')
		);
	}

	/**
	 * Create the form after the response was received successfully.
	 *
	 * @param response - the response data
	 */
	protected processResponse(response: KeyValueMap): void {
		if (typeof response.data !== 'undefined') {
			this.watch();

			Object.values(this.sections).forEach((section) => {
				section.innerHTML = '';
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
			const themes = getThemes();
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
}

/**
 * The template field button.
 *
 * @extends BaseComponent
 */
class FieldTemplateComponent extends BaseComponent {
	/**
	 * The field data.
	 *
	 * @param {KeyValueMap} params
	 * @param {KeyValueMap} params.pageData
	 * @param {KeyValueMap} params.shared
	 * @param {string} params.template
	 * @param {string} params.themeKey
	 */
	set data({ pageData, shared, template, themeKey }: TemplateFieldData) {
		this.render({ pageData, shared, template, themeKey });
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(classes.field);
	}

	/**
	 * Render a template field button.
	 *
	 * @param params
	 * @param params.pageData
	 * @param params.shared
	 * @param params.template
	 * @param params.themeKey
	 */
	private render({
		pageData,
		shared,
		template,
		themeKey,
	}: TemplateFieldData): void {
		const {
			buttonLabel,
			buttonIcon,
			buttonClass,
			selectedTemplate,
			mainTheme,
		} = themeStatus({
			pageData,
			shared,
			template,
			themeKey,
		});

		const button = create('div', [classes.field], {}, this);
		const modal = create(
			'am-modal',
			[],
			{ id: 'am-page-template-modal' },
			this
		);

		button.innerHTML = `
			<label class="${classes.fieldLabel}">${text('page_theme_template')}</label>
			<am-modal-toggle modal="#am-page-template-modal" class="am-e-button am-e-button--${buttonClass} am-u-flex">
				<i class="bi bi-${buttonIcon}"></i>
				<span class="am-u-flex__item-grow">
					${buttonLabel}
				</span>
				<i class="bi bi-pen"></i>
			</am-modal-toggle>
		`;

		modal.innerHTML = `
			<div class="${classes.modalDialog}">
				<div class="${classes.modalHeader}">
					<span>${text('page_theme_template')}</span>
					<am-modal-close class="${classes.modalClose}"></am-modal-close>
				</div>
				<am-form-page-select-template></am-form-page-select-template>
				<div class="${classes.modalFooter}">
					<am-modal-close class="${classes.button}">
						${text('btn_close')}
					</am-modal-close>
					<am-form-submit 
					class="${classes.button} ${classes.buttonSuccess}" 
					form="FormPageComponent::data"
					>
						${text('btn_apply_reload')}
					</am-form-submit>
				</div>
			</div>
		`;

		const select = query(
			'am-form-page-select-template',
			modal
		) as FieldSelectTemplateComponent;

		select.data = {
			value: selectedTemplate,
			mainTheme,
		};
	}
}

/**
 * The actual template select field.
 *
 * @extends BaseComponent
 */
class FieldSelectTemplateComponent extends BaseComponent {
	/**
	 * The field data.
	 *
	 * @param params
	 * @param params.value
	 * @param params.mainTheme
	 */
	set data({ value, mainTheme }: KeyValueMap) {
		this.render({ value, mainTheme });
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(classes.field);
	}

	/**
	 * Create a set of options for the a optgroup.
	 *
	 * @param templates - the templates array
	 * @param section - the section element
	 * @param value - the currently used template
	 * @param [themeName] - the optional theme name for the current group
	 * @param [themePath] - the optional theme path
	 */
	private createOptions(
		templates: string[],
		section: SwitcherSectionComponent,
		value: string,
		themeName: string = '*',
		themePath: string = ''
	): void {
		templates.forEach((template) => {
			const file = templatePath(template, themePath);
			const option = create('option', [], { value: file }, section);

			option.textContent = templateName(file, themeName);

			if (value == file) {
				option.setAttribute('selected', '');
			}
		});
	}

	/**
	 * Render the field.
	 *
	 * @param params
	 * @param params.value
	 * @param params.mainTheme
	 */
	private render({ value, mainTheme }: KeyValueMap): void {
		const themes = getThemes();
		const select = create(
			'select',
			[classes.input],
			{ name: 'theme_template' },
			this
		);

		const mainGroup = create('optgroup', [], { label: '*' }, select);

		this.createOptions(mainTheme.templates, mainGroup, value);

		Object.values(themes).forEach((theme: KeyValueMap) => {
			const group = create('optgroup', [], { label: theme.name }, select);

			this.createOptions(
				theme.templates,
				group,
				value,
				theme.name,
				theme.path
			);
		});
	}
}

/**
 * A tags input field.
 *
 * @extends FieldComponent
 */
class FieldTagsComponent extends FieldComponent {
	/**
	 * Create the input field.
	 */
	input(): void {
		const { name, id, value } = this._data;
		const textarea = create(
			'textarea',
			[classes.input],
			{
				name,
				id,
			},
			this
		);

		textarea.innerHTML = value;

		new Tagify(textarea, {
			whitelist: getTags(),
			originalInputValueFormat: (tags) =>
				tags.map((item) => item.value).join(', '),
		});
	}
}

customElements.define('am-form-page-field-template', FieldTemplateComponent);
customElements.define(
	'am-form-page-select-template',
	FieldSelectTemplateComponent
);
customElements.define('am-form-page-field-tags', FieldTagsComponent);
customElements.define('am-form-page', PageFormComponent);
