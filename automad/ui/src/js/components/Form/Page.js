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
} from '../../utils/core';
import { create } from '../../utils/create';
import { Field } from '../Field';
import { Form } from '../Form';

/**
 * Create a form field and set its data.
 *
 * @param {string} fieldType - The field type name
 * @param {HTMLElement} section - the section node where the field is created in
 * @param {Object} data - the field data object
 * @param {Array} cls - the array with optional class name
 * @returns {Field} - the generated field
 */
const createField = (fieldType, section, data, cls = []) => {
	const field = create(fieldType, cls, {}, section);

	field.data = data;

	return field;
};

/**
 * Create a group of form fields within a given section element based on a set of keys.
 *
 * @param {Object} params
 * @param {HTMLElement} params.section - the section node where the field is created in
 * @param {Array} params.keys - the array of variable keys for the field group
 * @param {Object} params.pageData - the data object that was loaded from the page's data file
 * @param {Object} params.tooltips - the field tooltips
 * @param {boolean} params.removable - true if the field should be removable
 */
const fieldGroup = ({ section, keys, pageData, tooltips, removable }) => {
	keys.forEach((key) => {
		let fieldType = 'am-field-textarea';

		if (key.startsWith('+')) {
			fieldType = 'am-field-editor';
		}

		if (key.startsWith('checkbox')) {
			fieldType = 'am-field-checkbox-page';
		}

		if (key.startsWith('color')) {
			fieldType = 'am-field-color';
		}

		if (key.startsWith('date')) {
			fieldType = 'am-field-date';
		}

		if (key.startsWith('text')) {
			fieldType = 'am-field-markdown';
		}

		if (key.startsWith('image')) {
			fieldType = 'am-field-image';
		}

		if (key.startsWith('url')) {
			fieldType = 'am-field-url';
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
 * @param {string} template - the path of a given template
 * @param {string} [themeName] - an optional theme name
 * @returns {string} the beautified template name
 */
const templateName = (template, themeName = '') => {
	const templateName = template
		.split('/')
		.reverse()[0]
		.replace(/\.php$/g, '');

	return titleCase([themeName, templateName].join('/'));
};

/**
 * Simplify a template/theme path combination to represent a theme variable value.
 *
 * @param {string} template - a template path
 * @param {string} [path] - an optional path
 * @returns {string}
 */
const templatePath = (template, path = '') => {
	const templateName = template
		.split('/')
		.reverse()[0]
		.replace(/\.php$/g, '');

	return [path, templateName].filter((item) => item.length).join('/');
};

/**
 * Get all status info about the selected template.
 *
 * @param {Object} params
 * @param {Object} params.pageData - the data object that was loaded from the page's data file
 * @param {Object} params.shared - the shared data object
 * @param {string} params.template - a template path
 * @param {string} params.themeKey - the field name for themes
 * @returns {{buttonLabel: string, buttonClass: string, buttonIcon: string, selectedTemplate: string, mainTheme: Object}}
 */
const themeStatus = ({ pageData, shared, template, themeKey }) => {
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
 * A page data form element.
 * The Page class doesn't need the watch and init properties
 * as this is anyways the intended behavior.
 * ```
 * <am-form-page controller="Page::data" page="/url"></am-form-page>
 * <am-form-submit form="Page::data">Submit</am-form-submit>
 * ```
 *
 * @extends Form
 */
class Page extends Form {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		this.sections = this.createSections();

		this.submit();
	}

	/**
	 * Create switcher sections for the different kind of variable fields.
	 *
	 * @returns {Object}
	 */
	createSections() {
		const sections = {};
		const content = getSwitcherSections().content;

		['settings', 'text', 'colors', 'unused'].forEach((key) => {
			const section = create(
				'am-switcher-section',
				[],
				{ name: content[key] },
				this
			);

			create('div', [classes.spinner], {}, section);
			sections[key] = section;
		});

		return sections;
	}

	/**
	 * Create the main settings fields.
	 *
	 * @param {Object} params
	 * @param {HTMLElement} params.section - the section element where the fields are created in
	 * @param {string} params.url - the page URL
	 * @param {string} params.prefix - the directory prefix
	 * @param {string} params.slug - the page slug
	 * @param {Object} params.pageData - the data that was loaded from the data file
	 * @param {Object} params.shared - the shared data object
	 * @param {Object} params.reserved - the reserved keys object
	 * @param {string} params.template - the page template path
	 */
	mainSettings({
		section,
		url,
		prefix,
		slug,
		pageData,
		shared,
		reserved,
		template,
	}) {
		const allTags = getTags();

		/**
		 * Create a field for one of the main settings.
		 *
		 * @param {string} fieldType
		 * @param {string} key
		 * @param {string} [label]
		 * @returns {Field}
		 */
		const createMainField = (fieldType, key, label = '') => {
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

		const tagsField = createMainField(
			'am-form-page-field-tags',
			reserved['AM_KEY_TAGS'],
			text('page_tags')
		);

		tagsField.init(allTags);
	}

	/**
	 * Create the form after the response was received successfully.
	 *
	 * @param {Object} response - the response data
	 */
	processResponse(response) {
		if (typeof response.data === 'undefined') {
			return false;
		}

		this.watch();

		Object.values(this.sections).forEach((section) => {
			section.innerHTML = '';
		});

		const { url, prefix, slug, pageData, shared, template, keys } =
			response.data;

		const themeKey = keys.reserved['AM_KEY_THEME'];
		const themes = getThemes();

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
			reserved: keys.reserved,
			template,
		});

		Object.keys(this.sections).forEach((item) => {
			fieldGroup({
				section: this.sections[item],
				keys: keys[item],
				pageData,
				tooltips,
				removable: item == 'unused',
			});
		});
	}
}

/**
 * The template field button.
 *
 * @extends Field
 */
class FieldTemplate extends Field {
	/**
	 * The field data.
	 *
	 * @param {Object} params
	 * @param {Object} params.pageData
	 * @param {Object} params.shared
	 * @param {string} params.template
	 * @param {string} params.themeKey
	 */
	set data({ pageData, shared, template, themeKey }) {
		this.render({ pageData, shared, template, themeKey });
	}

	/**
	 * Render a template field button.
	 *
	 * @param {Object} params
	 * @param {Object} params.pageData
	 * @param {Object} params.shared
	 * @param {string} params.template
	 * @param {string} params.themeKey
	 */
	render({ pageData, shared, template, themeKey }) {
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

		const button = create('div', [this.cls.field], {}, this);
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
					<am-form-submit class="${classes.button} ${
			classes.buttonSuccess
		}" form="Page::data">
						${text('btn_apply_reload')}
					</am-form-submit>
				</div>
			</div>
		`;

		const select = query('am-form-page-select-template', modal);

		select.data = {
			value: selectedTemplate,
			mainTheme,
		};
	}
}

/**
 * The actual template select field.
 *
 * @extends Field
 */
class FieldSelectTemplate extends Field {
	/**
	 * The field data.
	 *
	 * @param {Object} params
	 * @param {string} params.value
	 * @param {Object} params.mainTheme
	 */
	set data({ value, mainTheme }) {
		this.render({ value, mainTheme });
	}

	/**
	 * Create a set of options for the a optgroup.
	 *
	 * @param {Array} templates - the templates array
	 * @param {HTMLElement} section - the section element
	 * @param {string} value - the currently used template
	 * @param {string} [themeName] - the optional theme name for the current group
	 * @param {string} [themePath] - the optional theme path
	 */
	createOptions(templates, section, value, themeName = '*', themePath = '') {
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
	 * @param {Object} params
	 * @param {string} params.value
	 * @param {Object} params.mainTheme
	 */
	render({ value, mainTheme }) {
		const themes = getThemes();
		const select = create(
			'select',
			[classes.input],
			{ name: 'theme_template' },
			this
		);

		const mainGroup = create('optgroup', [], { label: '*' }, select);

		this.createOptions(mainTheme.templates, mainGroup, value);

		Object.values(themes).forEach((theme) => {
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
 * @extends Field
 */
class FieldTags extends Field {
	/**
	 * Create the input field.
	 */
	input() {
		const { name, id, value } = this._data;
		this.field = create(
			'textarea',
			[classes.input],
			{
				name,
				id,
			},
			this
		);

		this.field.innerHTML = value;
	}

	/**
	 * Init a new Tagify instance.
	 *
	 * @see {@link options https://github.com/yairEO/tagify}
	 * @param {Array} allTags - the array for tags autocompletion
	 */
	init(allTags) {
		const tagify = new Tagify(this.field, {
			whitelist: allTags,
			originalInputValueFormat: (tags) =>
				tags.map((item) => item.value).join(', '),
		});
	}
}

customElements.define('am-form-page-field-template', FieldTemplate);
customElements.define('am-form-page-select-template', FieldSelectTemplate);
customElements.define('am-form-page-field-tags', FieldTags);
customElements.define('am-form-page', Page);
