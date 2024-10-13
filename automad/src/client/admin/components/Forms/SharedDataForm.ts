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
 * Copyright (c) 2023-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	Binding,
	collectFieldData,
	create,
	createCustomizationFields,
	createField,
	createFieldSections,
	EventName,
	fieldGroup,
	FieldTag,
	fire,
	listen,
	prepareFieldGroups,
} from '@/admin/core';
import {
	DeduplicationSettings,
	FieldSectionCollection,
	FieldSectionName,
	KeyValueMap,
	SharedBindings,
} from '@/admin/types';
import { FormComponent } from './Form';

/**
 * Init all URL and slug related bindings.
 *
 * @param response
 * @return the page bindings object
 */
const createBindings = (response: KeyValueMap): SharedBindings => {
	const sharedDataFetchTimeBinding = new Binding('sharedDataFetchTime', {
		initial: response.time,
	});

	return {
		sharedDataFetchTimeBinding,
	};
};

/**
 * The shared data form element.
 *
 * @example
 * <am-shared-data-form ${Attr.api}="SharedController::data"></am-shared-data-form>
 *
 * @extends FormComponent
 */
export class SharedDataFormComponent extends FormComponent {
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
	 * The bindings object.
	 */
	private bindings: SharedBindings;

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

				this.bindings.sharedDataFetchTimeBinding.value = Math.ceil(
					new Date().getTime() / 1000
				);
			})
		);
	}

	/**
	 * Create the form after the response was received successfully.
	 *
	 * @param response - the response data
	 * @async
	 */
	protected async processResponse(response: KeyValueMap): Promise<void> {
		await super.processResponse(response);

		if (response.code !== 200) {
			return;
		}

		fire(EventName.contentSaved);

		if (this.bindings) {
			this.bindings.sharedDataFetchTimeBinding.value = response.time;
		}

		if (!response.data) {
			return;
		}

		if (!this.bindings) {
			this.bindings = createBindings(response);
		}

		this.render(response);
	}

	/**
	 * Create the actual form fields.
	 *
	 * @param response
	 */
	private render(response: KeyValueMap): void {
		const { fields } = response.data;
		const themeKey = App.reservedFields.THEME;
		const mainTheme = fields[themeKey] || Object.keys(App.themes)[0];
		const tooltips = App.themes[mainTheme]?.tooltips ?? {};
		const fieldGroups = prepareFieldGroups(fields);

		create(
			'input',
			[],
			{
				type: 'hidden',
				[Attr.bind]: 'sharedDataFetchTime',
				[Attr.bindTo]: 'value',
				name: 'dataFetchTime',
			},
			this
		);

		const sitenameField = createField(
			FieldTag.title,
			this.sections.settings,
			{
				key: App.reservedFields.SITENAME,
				value: fields[App.reservedFields.SITENAME],
				name: `data[${App.reservedFields.SITENAME}]`,
			},
			[],
			{ required: '' }
		);

		// Used for sidebar top link.
		new Binding('sitename', {
			input: sitenameField.input,
			modifier: (sitename: string) => {
				return sitename;
			},
		});

		createField(FieldTag.mainTheme, this.sections.settings, {
			key: themeKey,
			value: mainTheme,
			name: `data[${themeKey}]`,
		});

		createField(FieldTag.syntaxSelect, this.sections.settings, {
			key: App.reservedFields.SYNTAX_THEME,
			value: fields[App.reservedFields.SYNTAX_THEME],
			name: `data[${App.reservedFields.SYNTAX_THEME}]`,
		});

		createCustomizationFields(fields, this.sections);

		Object.keys(this.sections).forEach((item: FieldSectionName) => {
			fieldGroup({
				section: this.sections[item],
				fields: fieldGroups[item],
				tooltips,
			});
		});
	}
}

customElements.define('am-shared-data-form', SharedDataFormComponent);
