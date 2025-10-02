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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	create,
	createProgressModal,
	CSS,
	FieldTag,
	html,
	listen,
	query,
	titleCase,
} from '@/admin/core';
import {
	KeyValueMap,
	TemplateButtonStatus,
	TemplateFieldData,
} from '@/admin/types';
import { BaseComponent } from '@/admin/components/Base';

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
 * @param params.fields - the data object that was loaded from the page's data file
 * @param params.shared - the shared data object
 * @param params.template - a template path
 * @param params.themeKey - the field name for themes
 * @returns the UI items the represent a theme status
 */
const themeStatus = ({
	fields,
	template,
	themeKey,
}: TemplateFieldData): TemplateButtonStatus => {
	const themes = App.themes;
	let mainTheme = themes[App.mainTheme];

	if (typeof mainTheme == 'undefined') {
		mainTheme = Object.values(themes)[0];
	}

	let templateExists = false;
	let appliedTheme = mainTheme;
	let selectedTemplate = templatePath(template);
	let buttonLabel = titleCase(selectedTemplate);
	let buttonIcon = 'code-slash';

	if (typeof themes[fields[themeKey]] != 'undefined') {
		appliedTheme = themes[fields[themeKey]];
		buttonLabel = titleCase(`${appliedTheme.name}/${selectedTemplate}`);
		selectedTemplate = templatePath(template, appliedTheme.path);
	}

	templateExists = appliedTheme?.templates.indexOf(template) !== -1;

	if (!templateExists) {
		buttonIcon = 'question-circle';
	}

	return {
		buttonLabel,
		buttonIcon,
		selectedTemplate,
	};
};

/**
 * Create a set of options for the a optgroup.
 *
 * @param templates - the templates array
 * @param element - the section element
 * @param value - the currently used template
 * @param [themeName] - the optional theme name for the current group
 * @param [themePath] - the optional theme path
 */
const createOptions = (
	templates: string[],
	element: HTMLElement,
	value: string,
	themeName: string = '*',
	themePath: string = ''
): void => {
	templates.forEach((template) => {
		const file = templatePath(template, themePath);
		const option = create('option', [], { value: file }, element);

		option.textContent = templateName(file, themeName);

		if (value == file) {
			option.setAttribute('selected', '');
		}
	});
};

/**
 * The template select.
 *
 * @extends BaseComponent
 */
class PageTemplateSelectComponent extends BaseComponent {
	connectedCallback(): void {
		this.classList.add(CSS.select);

		const selectedTemplate = this.getAttribute('value');
		const mainTheme =
			App.themes[App.mainTheme] || App.themes[Object.keys(App.themes)[0]];
		const themes = App.themes;
		const label = create('span', [], {}, this);
		const select = create(
			'select',
			[],
			{
				name: 'theme_template',
			},
			this
		) as HTMLSelectElement;

		const mainGroup = create('optgroup', [], { label: '*' }, select);

		createOptions(mainTheme?.templates ?? [], mainGroup, selectedTemplate);

		Object.values(themes).forEach((theme: KeyValueMap) => {
			const group = create('optgroup', [], { label: theme.name }, select);

			createOptions(
				theme.templates,
				group,
				selectedTemplate,
				theme.name,
				theme.path
			);
		});

		const update = (showProgress: boolean = false) => {
			label.textContent = select.options[select.selectedIndex].text;

			if (showProgress) {
				createProgressModal(
					App.text('switchingTemplateProgress')
				).open();
			}
		};

		listen(select, 'change', update.bind(this, true));

		setTimeout(update, 0);
	}
}

/**
 * The template field button.
 *
 * @extends BaseComponent
 */
export class PageTemplateFieldComponent extends BaseComponent {
	/**
	 * The field data.
	 *
	 * @param params
	 * @param params.fields
	 * @param params.template
	 * @param params.themeKey
	 * @param params.readme
	 */
	set data({ fields, template, themeKey, readme }: TemplateFieldData) {
		this.render({ fields, template, themeKey, readme });
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.field);
	}

	/**
	 * Render a template field button.
	 *
	 * @param params
	 * @param params.fields
	 * @param params.template
	 * @param params.themeKey
	 * @param params.readme
	 */
	private render({
		fields,
		template,
		themeKey,
		readme,
	}: TemplateFieldData): void {
		const { buttonLabel, buttonIcon, selectedTemplate } = themeStatus({
			fields,
			template,
			themeKey,
			readme,
		});

		this.innerHTML = html`
			<label class="${CSS.fieldLabel}">${App.text('pageTemplate')}</label>
			<am-modal-toggle
				${Attr.modal}="#am-page-template-modal"
				class="${CSS.input} ${CSS.flex} ${CSS.flexAlignCenter} ${CSS.flexBetween} ${CSS.cursorPointer}"
			>
				<am-icon-text
					${Attr.icon}="${buttonIcon}"
					${Attr.text}="${buttonLabel}"
				></am-icon-text>
				<i class="bi bi-pen"></i>
			</am-modal-toggle>
		`;

		if (readme) {
			const readmeLink = create(
				'a',
				[],
				{ href: readme, target: '_blank' },
				this
			);

			create(
				'am-icon-text',
				[],
				{
					[Attr.icon]: 'file-earmark-text',
					[Attr.text]: App.text('themeReadme'),
				},
				readmeLink
			);
		}

		create(
			'am-modal',
			[],
			{ id: 'am-page-template-modal' },
			this,
			html`
				<am-modal-dialog>
					<am-modal-header>
						${App.text('pageTemplate')}
					</am-modal-header>
					<am-modal-body></am-modal-body>
				</am-modal-dialog>
			`
		);

		const body = query('am-modal-body', this);

		if (Object.keys(App.themes).length > 0) {
			body.innerHTML = html`
				<p>${App.text('pageTemplatePurgeUnusedInfo')}</p>
				<am-page-template-select
					value="${selectedTemplate}"
				></am-page-template-select>
			`;
		} else {
			body.textContent = App.text('noThemesFound');
		}
	}
}

customElements.define(FieldTag.pageTemplate, PageTemplateFieldComponent);
customElements.define('am-page-template-select', PageTemplateSelectComponent);
