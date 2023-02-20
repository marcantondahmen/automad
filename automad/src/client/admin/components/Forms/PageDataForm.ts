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
 * Copyright (c) 2021-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	FieldSectionCollection,
	FieldSectionName,
	KeyValueMap,
	PageBindings,
	PageMainSettingsData,
} from '../../types';
import { InputComponent } from '../Fields/Input';
import { FormComponent } from './Form';
import {
	App,
	Attr,
	Binding,
	create,
	createField,
	createFieldSections,
	CSS,
	fieldGroup,
	getPageURL,
	html,
	prepareFieldGroups,
	Route,
	setDocumentTitle,
} from '../../core';
import { PageTemplateComponent } from '../Fields/PageTemplate';
import { getLogger } from '../../core/logger';

/**
 * Init all URL and slug related bindings.
 *
 * @param response
 * @return the page bindings object
 */
const createBindings = (response: KeyValueMap): PageBindings => {
	const { slug, url } = response.data;

	const pageDataFetchTimeBinding = new Binding('pageDataFetchTime', {
		initial: response.time,
	});

	const slugBinding = new Binding('pageSlug', {
		initial: slug,
	});
	const pageUrlBinding = new Binding('pageUrl', {
		initial: url,
	});

	const pageUrlWithBaseBinding = new Binding('pageUrlWithBase', {
		modifier: (url: string) => {
			return `${App.baseURL}${url}`;
		},
		initial: url,
	});

	const pageLinkUIBinding = new Binding('pageLinkUI', {
		modifier: (url: string) => {
			return `${Route.page}?url=${encodeURIComponent(url)}`;
		},
		initial: url,
	});

	return {
		slugBinding,
		pageUrlBinding,
		pageUrlWithBaseBinding,
		pageLinkUIBinding,
		pageDataFetchTimeBinding,
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
	if (!update.slug || !update.origUrl) {
		return;
	}

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
	private sections: FieldSectionCollection;

	/**
	 * The page bindings object.
	 */
	private bindings: PageBindings;

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
		this.sections = createFieldSections(this);

		super.init();
	}

	/**
	 * Create the main settings fields.
	 *
	 * @param params
	 */
	private mainSettings({
		section,
		url,
		fields,
		template,
		readme,
	}: PageMainSettingsData): void {
		/**
		 * Create a field for one of the main settings.
		 *
		 * @param fieldType
		 * @param key
		 * @param [label]
		 * @param [attributes]
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
			App.reservedFields.TITLE,
			'',
			{
				required: '',
			}
		);

		new Binding('title', {
			input: titleField.input,
			modifier: (title: string) => {
				setDocumentTitle(title);

				return title;
			},
		});

		create(
			'a',
			[],
			{
				href: `${App.baseURL}${fields[App.reservedFields.URL]}`,
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
			App.reservedFields.PRIVATE,
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
			themeKey: App.reservedFields.THEME,
		};

		const readmeLink = create(
			'a',
			[],
			{ href: readme, target: '_blank' },
			templateField
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

		createMainField(
			'am-toggle',
			App.reservedFields.HIDDEN,
			App.text('hidePage')
		);

		createMainField('am-date', App.reservedFields.DATE, App.text('date'));

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
			App.reservedFields.URL,
			App.text('redirectPage')
		);

		createMainField(
			'am-page-tags',
			App.reservedFields.TAGS,
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

		if (response.code === 404) {
			this.pageNotFound();
			getLogger().error('Page not found');

			return;
		}

		if (response.code !== 200) {
			return;
		}

		if (this.bindings) {
			this.bindings.pageDataFetchTimeBinding.value = response.time;
		}

		if (!response.data) {
			return;
		}

		if (!this.bindings) {
			this.bindings = createBindings(response);
		}

		if (response.data.update) {
			await updateUI(response.data.update, this.bindings);

			return;
		}

		this.render(response);
	}

	/**
	 * Create the actual form fields.
	 *
	 * @param response
	 */
	private render(response: KeyValueMap): void {
		const { url, fields, shared, template, readme } = response.data;

		create(
			'input',
			[],
			{
				type: 'hidden',
				[Attr.bind]: 'pageDataFetchTime',
				[Attr.bindTo]: 'value',
				name: 'dataFetchTime',
			},
			this
		);

		const themeKey = App.reservedFields.THEME;
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
			readme,
		});

		Object.keys(this.sections).forEach((item: FieldSectionName) => {
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
