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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import Tagify from '@yaireo/tagify';
import {
	getBaseURL,
	getSwitcherSections,
	query,
	text,
	titleCase,
} from '../../utils/core';
import { create } from '../../utils/create';
import { Field } from '../Field';
import { Form } from '../Form';

/**
 * The Page class doesn't need the watch and init properties
 * as this is anyways the intended behavior.
 * <am-form-page controller="Page::data" page="/url"></am-form-page>
 * <am-form-submit form="Page::data">
 *     Submit
 * </am-form-submit>
 */

const createField = (fieldType, section, data, cls = []) => {
	const field = create(fieldType, cls, {}, section);

	field.data = data;

	return field;
};

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

const templateName = (template, themeName = '') => {
	const templateName = template
		.split('/')
		.reverse()[0]
		.replace(/\.php$/g, '');

	return titleCase([themeName, templateName].join('/'));
};

const templatePath = (template, path = '') => {
	const templateName = template
		.split('/')
		.reverse()[0]
		.replace(/\.php$/g, '');

	return [path, templateName].filter((item) => item.length).join('/');
};

const themeStatus = ({ pageData, shared, themes, template, themeKey }) => {
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

class Page extends Form {
	connectedCallback() {
		this.sections = this.createSections();

		this.submit();
	}

	createSections() {
		const sections = {};
		const sectionIds = getSwitcherSections();
		const content = sectionIds.content;

		[
			content.settings,
			content.text,
			content.colors,
			content.unused,
		].forEach((name) => {
			const section = create('am-switcher-section', [], { name }, this);

			create('div', [this.cls.spinner], {}, section);
			sections[name] = section;
		});

		console.log(sections);

		return sections;
	}

	mainSettings({
		section,
		url,
		prefix,
		slug,
		pageData,
		shared,
		allTags,
		reserved,
		themes,
		template,
	}) {
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

		query('input', title).classList.add(this.cls.inputTitle);

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
				themes,
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

	processResponse(response) {
		if (typeof response.data === 'undefined') {
			return false;
		}

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
			allTags,
			themes,
			template,
			keys,
		} = response.data;

		let tooltips = {};
		const themeKey = keys.reserved['AM_KEY_THEME'];

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
			allTags,
			reserved: keys.reserved,
			themes,
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

class FieldTemplate extends Field {
	set data({ pageData, shared, themes, template, themeKey }) {
		this.render({ pageData, shared, themes, template, themeKey });
	}

	render({ pageData, shared, themes, template, themeKey }) {
		const {
			buttonLabel,
			buttonIcon,
			buttonClass,
			selectedTemplate,
			mainTheme,
		} = themeStatus({
			pageData,
			shared,
			themes,
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
			<label class="${this.cls.fieldLabel}">${text('page_theme_template')}</label>
			<am-modal-toggle modal="#am-page-template-modal" class="am-e-button am-e-button--${buttonClass} am-u-flex">
				<i class="bi bi-${buttonIcon}"></i>
				<span class="am-u-flex__item-grow">
					${buttonLabel}
				</span>
				<i class="bi bi-pen"></i>
			</am-modal-toggle>
		`;

		modal.innerHTML = `
			<div class="${this.cls.modalDialog}">
				<div class="${this.cls.modalHeader}">
					<span>${text('page_theme_template')}</span>
					<am-modal-close class="${this.cls.modalClose}"></am-modal-close>
				</div>
				<am-form-page-select-template></am-form-page-select-template>
				<div class="${this.cls.modalFooter}">
					<am-modal-close class="${this.cls.button}">
						${text('btn_close')}
					</am-modal-close>
					<am-form-submit class="${this.cls.button} ${
			this.cls.buttonSuccess
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
			themes,
		};
	}
}

class FieldSelectTemplate extends Field {
	set data({ value, themes, mainTheme }) {
		this.render({ value, themes, mainTheme });
	}

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

	render({ value, themes, mainTheme }) {
		const select = create(
			'select',
			[this.cls.input],
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

class FieldTags extends Field {
	input() {
		const { name, id, value } = this._data;
		this.field = create(
			'textarea',
			[this.cls.input],
			{
				name,
				id,
			},
			this
		);

		this.field.innerHTML = value;
	}

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
