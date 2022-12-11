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
	PageBindings,
	PageFieldGroups,
	PageMainSettingsData,
	PageSectionCollection,
	PageSectionName,
} from '../../types';
import { InputComponent } from '../Fields/Input';
import { FormComponent } from './Form';
import { SwitcherSectionComponent } from '../Switcher/SwitcherSection';
import {
	App,
	Attr,
	Binding,
	create,
	createField,
	CSS,
	getPageURL,
	html,
	Route,
	setDocumentTitle,
} from '../../core';
import { PageTemplateComponent } from '../Fields/PageTemplate';
import { Section } from '../Switcher/Switcher';

/**
 * Create a group of form fields within a given section element based on a set of keys.
 *
 * @param params
 * @param params.section - the section node where the field is created in
 * @param params.keys - the array of variable keys for the field group
 * @param params.pageData - the data object that was loaded from the page's data file
 * @param params.tooltips - the field tooltips
 * @param params.shared - the shared fallback data
 */
const fieldGroup = ({
	section,
	fields,
	tooltips,
	shared,
}: FieldGroupData): void => {
	const prefixMap = {
		'+': 'am-editor',
		checkbox: 'am-toggle-select',
		color: 'am-color',
		date: 'am-date',
		text: 'am-markdown',
		image: 'am-image-select',
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
				placeholder: shared[key],
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
const createSections = (form: PageDataFormComponent): PageSectionCollection => {
	const createSection = (section: string): SwitcherSectionComponent => {
		return create('am-switcher-section', [], { name: section }, form);
	};

	const sections: PageSectionCollection = {
		settings: createSection(Section.settings),
		text: createSection(Section.text),
		colors: createSection(Section.colors),
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
 * Init all URL and slug related bindings.
 *
 * @param data
 * @return the page bindings object
 */
const createBindings = (data: KeyValueMap): PageBindings => {
	const { slug, url } = data;

	const slugBinding = new Binding('pageSlug', null, null, slug);
	const pageUrlBinding = new Binding('pageUrl', null, null, url);

	const pageUrlWithBaseBinding = new Binding(
		'pageUrlWithBase',
		null,
		(url: string) => {
			return `${App.baseURL}${url}`;
		},
		url
	);

	const pageLinkUIBinding = new Binding(
		'pageLinkUI',
		null,
		(url: string) => {
			return `${Route.page}?url=${encodeURIComponent(url)}`;
		},
		url
	);

	return {
		slugBinding,
		pageUrlBinding,
		pageUrlWithBaseBinding,
		pageLinkUIBinding,
	};
};

/**
 * Handle UI updates.
 *
 * @param update
 * @return a promise
 */
const updateUI = async (
	update: KeyValueMap,
	pageBindings: PageBindings
): Promise<void> => {
	if (update.slug && update.origUrl) {
		const lockId = App.addNavigationLock();

		const { slug, origUrl } = update;
		const urlObject = new URL(window.location.href);
		const {
			pageUrlBinding,
			pageUrlWithBaseBinding,
			pageLinkUIBinding,
			slugBinding,
		} = pageBindings;

		pageUrlBinding.value = origUrl;
		pageUrlWithBaseBinding.value = origUrl;
		pageLinkUIBinding.value = origUrl;
		slugBinding.value = slug;

		urlObject.searchParams.set('url', update.origUrl);
		window.history.replaceState(null, null, urlObject);

		await App.updateState();

		App.removeNavigationLock(lockId);
	}
};

/**
 * A page data form element.
 *
 * @example
 * <am-page-data-form ${Attr.api}="Page/data"></am-page-data-form>
 * <am-submit ${Attr.form}="Page/data">Submit</am-submit>
 *
 * @extends FormComponent
 */
export class PageDataFormComponent extends FormComponent {
	/**
	 * The section collection object.
	 */
	private sections: PageSectionCollection;

	/**
	 * The page bindings object.
	 */
	private pageBindings: PageBindings;

	/**
	 * Wait for pending requests.
	 */
	protected get parallel(): boolean {
		return false;
	}

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
		): InputComponent => {
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
				target: '_blank',
				[Attr.bind]: 'pageUrlWithBase',
				[Attr.bindTo]: 'href',
			},
			titleField
		).innerHTML = html`
			<span class="${CSS.iconText}">
				<i class="bi bi-link"></i>
				<span
					${Attr.bind}="pageUrlWithBase"
					${Attr.bindTo}="textContent"
				></span>
			</span>
		`;

		createMainField(
			'am-toggle-large',
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
			'am-toggle',
			App.reservedFields.AM_KEY_HIDDEN,
			App.text('hidePage')
		);

		createMainField(
			'am-date',
			App.reservedFields.AM_KEY_DATE,
			App.text('date')
		);

		if (url != '/') {
			createField(
				'am-input',
				section,
				{
					key: 'slug',
					value: null, // Defined by binding.
					name: 'slug',
					label: App.text('pageSlug'),
				},
				[],
				{ [Attr.bind]: 'pageSlug', [Attr.bindTo]: 'value' }
			);
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
				${Attr.icon}="x-circle"
				${Attr.text}="${App.text(
					'pageNotFoundError'
				)} ⟶ ${getPageURL()}"
			></am-alert>
		`;
	}

	/**
	 * Create the form after the response was received successfully.
	 *
	 * @param response - the response data
	 * @async
	 */
	protected async processResponse(response: KeyValueMap): Promise<void> {
		await super.processResponse(response);

		if (response.code != 200) {
			this.pageNotFound();

			return;
		}

		if (!response.data) {
			return;
		}

		if (response.data.update) {
			await updateUI(response.data.update, this.pageBindings);

			return;
		}

		this.pageBindings = createBindings(response.data);
		this.render(response.data);
	}

	/**
	 * Create the actual form fields.
	 *
	 * @param data
	 */
	private render(data: KeyValueMap): void {
		const { url, fields, shared, template } = data;

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
			fields,
			template,
		});

		Object.keys(this.sections).forEach((item: PageSectionName) => {
			fieldGroup({
				section: this.sections[item],
				fields: fieldGroups[item],
				tooltips,
				shared,
			});
		});
	}
}

customElements.define('am-page-data-form', PageDataFormComponent);
