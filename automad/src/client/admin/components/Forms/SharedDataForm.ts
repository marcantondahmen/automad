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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	Binding,
	create,
	createField,
	createFieldSections,
	fieldGroup,
	prepareFieldGroups,
} from '../../core';
import {
	FieldSectionCollection,
	FieldSectionName,
	KeyValueMap,
	SharedBindings,
} from '../../types';
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
 * <am-shared-data-form ${Attr.api}="Shared/data"></am-shared-data-form>
 *
 * @extends FormComponent
 */
export class SharedDataFormComponent extends FormComponent {
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
		const themeKey = App.reservedFields.AM_KEY_THEME;
		const mainTheme = fields[themeKey] || Object.keys(App.themes)[0];
		const tooltips = App.themes[mainTheme].tooltips;
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

		createField(
			'am-title',
			this.sections.settings,
			{
				key: App.reservedFields.AM_KEY_SITENAME,
				value: fields[App.reservedFields.AM_KEY_SITENAME],
				name: `data[${App.reservedFields.AM_KEY_SITENAME}]`,
			},
			[],
			{ required: '' }
		);

		createField('am-main-theme', this.sections.settings, {
			key: themeKey,
			value: mainTheme,
			name: `data[${themeKey}]`,
		});

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
