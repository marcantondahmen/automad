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
	DeduplicationSettings,
	FieldSectionCollection,
	FieldSectionName,
	KeyValueMap,
	PageBindings,
	PageMainSettingsData,
	Theme,
} from '@/admin/types';
import { FormComponent } from './Form';
import {
	App,
	Attr,
	Binding,
	collectFieldData,
	create,
	createCustomizationFields,
	createField,
	createFieldSections,
	createLabelFromField,
	CSS,
	EventName,
	fieldGroup,
	FieldTag,
	fire,
	getLogger,
	getPageURL,
	html,
	listen,
	prepareFieldGroups,
	setDocumentTitle,
} from '@/admin/core';
import { PageTemplateFieldComponent } from '@/admin/components/Fields/PageTemplateField';
import { BaseFieldComponent } from '@/admin/components/Fields/BaseField';

/**
 * Init all URL and slug related bindings.
 *
 * @param response
 * @return the page bindings object
 */
const createBindings = (response: KeyValueMap): PageBindings => {
	const pageDataFetchTimeBinding = new Binding('pageDataFetchTime', {
		initial: response.time,
	});

	const slugBinding = new Binding('pageSlug', {
		initial: response.data.fields[App.reservedFields.SLUG],
	});

	return {
		slugBinding,
		pageDataFetchTimeBinding,
	};
};

/**
 * A page data form element.
 *
 * @example
 * <am-page-data-form ${Attr.api}="PageController::data"></am-page-data-form>
 * <am-submit ${Attr.form}="PageController::data">Submit</am-submit>
 *
 * @extends FormComponent
 */
export class PageDataFormComponent extends FormComponent {
	/**
	 * The deduplication settings for the form.
	 */
	protected get deduplicationSettings(): DeduplicationSettings {
		return {
			getFormData: (element) => {
				const data = collectFieldData(element);

				data.dataFetchTime = null;

				return data;
			},
			enabled: true,
		};
	}

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

		this.addListener(
			listen(window, EventName.contentPublished, () => {
				if (!this.bindings) {
					return;
				}

				this.bindings.pageDataFetchTimeBinding.value = Math.ceil(
					new Date().getTime() / 1000
				);
			})
		);
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
			fieldType: FieldTag,
			key: string,
			label: string = '',
			attributes: KeyValueMap = {},
			container: HTMLElement = section
		): BaseFieldComponent => {
			const data = {
				key,
				value: fields[key],
				name: `data[${key}]`,
				label,
			};

			return createField(fieldType, container, data, [], attributes);
		};

		const titleContainer = create(
			'div',
			[CSS.flex, CSS.flexColumn, CSS.flexGap],
			{},
			section
		);

		const titleField = createMainField(
			FieldTag.title,
			App.reservedFields.TITLE,
			'',
			{
				required: '',
			},
			titleContainer
		);

		// Used in breadcrumbs and nav tree.
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
				href: `${App.baseIndex}${url}`,
			},
			titleContainer
		).innerHTML = html`
			<span class="${CSS.iconText}">
				<i class="bi bi-link"></i>
				<span>${App.baseIndex}${url}</span>
			</span>
		`;

		createMainField(
			FieldTag.toggleLarge,
			App.reservedFields.PRIVATE,
			App.text('keepPagePrivate')
		);

		const templateField = create(
			FieldTag.pageTemplate,
			[],
			{},
			section
		) as PageTemplateFieldComponent;

		templateField.data = {
			fields,
			template,
			themeKey: App.reservedFields.THEME,
			readme,
		};

		createMainField(
			FieldTag.syntaxSelect,
			App.reservedFields.SYNTAX_THEME,
			createLabelFromField(App.reservedFields.SYNTAX_THEME)
		);

		createMainField(
			FieldTag.toggle,
			App.reservedFields.HIDDEN,
			App.text('hidePage')
		);

		createMainField(
			FieldTag.date,
			App.reservedFields.DATE,
			App.text('date')
		);

		if (url != '/') {
			createField(
				FieldTag.input,
				section,
				{
					key: App.reservedFields.SLUG,
					value: fields[App.reservedFields.SLUG], // Defined by binding.
					name: `data[${App.reservedFields.SLUG}]`,
					label: App.text('pageSlug'),
				},
				[],
				{ [Attr.bind]: 'pageSlug', [Attr.bindTo]: 'value' }
			);
		}

		createMainField(
			FieldTag.url,
			App.reservedFields.URL,
			App.text('redirectPage')
		);

		createMainField(
			FieldTag.pageTags,
			App.reservedFields.TAGS,
			App.text('pageTags')
		);

		createMainField(
			FieldTag.input,
			App.reservedFields.META_TITLE,
			App.text('metaTitle')
		);

		createMainField(
			FieldTag.textarea,
			App.reservedFields.META_DESCRIPTION,
			App.text('metaDescription')
		);

		createMainField(
			FieldTag.image,
			App.reservedFields.OPEN_GRAPH_IMAGE,
			App.text('openGraphImage')
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

		fire(EventName.contentSaved);

		if (this.bindings) {
			this.bindings.pageDataFetchTimeBinding.value = response.time;
		}

		if (!response.data) {
			return;
		}

		if (!this.bindings) {
			this.bindings = createBindings(response);
		}

		if (response.data.updateUI) {
			this.bindings.slugBinding.value = response.data.slug ?? '';

			await App.updateState();

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

		if (!fields) {
			return;
		}

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

		let theme: Theme | null = null;

		if (fields[themeKey]) {
			theme = themes[fields[themeKey]];
		} else {
			theme = themes[shared[themeKey]];
		}

		const labels = theme?.labels ?? {};
		const tooltips = theme?.tooltips ?? {};
		const themeOptions = theme?.options ?? {};

		const fieldGroups = prepareFieldGroups(fields);

		this.mainSettings({
			section: this.sections.settings,
			url,
			fields,
			template,
			readme,
		});

		createCustomizationFields(fields, this.sections, shared);

		Object.keys(this.sections).forEach((item: FieldSectionName) => {
			fieldGroup({
				section: this.sections[item],
				fields: fieldGroups[item],
				labels,
				tooltips,
				themeOptions,
				renderEmptyAlert: item != 'customizations',
				shared,
			});
		});
	}
}

customElements.define('am-page-data-form', PageDataFormComponent);
