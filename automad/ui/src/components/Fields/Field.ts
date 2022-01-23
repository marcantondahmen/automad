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

import { classes, htmlSpecialChars, titleCase } from '../../core/utils';
import { create } from '../../core/create';
import { BaseComponent } from '../Base';
import { App } from '../../core/app';

interface FieldInitData {
	key: string;
	value: string;
	name: string;
	tooltip: string;
	label: string;
	removable: boolean;
}

interface FieldRenderData extends Omit<FieldInitData, 'key'> {
	id: string;
}

/**
 * Create an ID from a field key.
 *
 * @param key
 * @returns the generated ID
 */
const createId = (key: string): string => {
	return `am-field__${key.replace(/(?!^)([A-Z])/g, '-$1').toLowerCase()}`;
};

/**
 * Create a label text from a field key.
 *
 * @param key
 * @returns the generated label
 */
const createLabel = (key: string): string => {
	return titleCase(key.replace(/\+(.)/, '+ $1'))
		.replace('+ ', '+')
		.replace('Color ', '')
		.replace('Checkbox ', '');
};

/**
 * A standard input field with a label.
 *
 * @extends BaseComponent
 */
export class FieldComponent extends BaseComponent {
	/**
	 * The internal field data.
	 */
	protected _data: FieldRenderData;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(classes.field);
	}

	/**
	 * The field data.
	 *
	 * @param params
	 * @param params.key
	 * @param params.value
	 * @param params.name
	 * @param params.tooltip
	 * @param params.label
	 * @param params.removable
	 */
	set data({ key, value, name, tooltip, label, removable }: FieldInitData) {
		const id = createId(key);

		value = value || '';
		tooltip = tooltip || '';
		label = label || createLabel(key);

		if (removable) {
			label = `${label} (${App.text('page_var_unused')})`;
		}

		if (typeof value === 'string') {
			value = htmlSpecialChars(value);
		}

		this._data = {
			name,
			id,
			label,
			value,
			tooltip,
			removable,
		};

		this.render();
	}

	/**
	 * Create a label.
	 */
	label(): void {
		const { id, label, tooltip, removable } = this._data;
		const removeButton = removable
			? '<am-remove-field><i class="bi bi-trash"></i></am-remove-field>'
			: '';
		const wrapper = create('div', [], {}, this);

		wrapper.innerHTML = `
			<label 
			class="${classes.fieldLabel}" 
			for="${id}" 
			title="${tooltip}">
				${label}
			</label>
			${removeButton}
		`;
	}

	/**
	 * Create an input field.
	 */
	input(): void {
		const { name, id, value } = this._data;
		create(
			'input',
			[classes.input],
			{ id, name, value, type: 'text' },
			this
		);
	}

	/**
	 * Render the field.
	 */
	render(): void {
		this.label();
		this.input();
	}
}

customElements.define('am-field', FieldComponent);
