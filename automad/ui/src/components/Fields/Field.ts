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

import {
	App,
	classes,
	create,
	html,
	htmlSpecialChars,
	titleCase,
} from '../../core';
import { BaseComponent } from '../Base';

interface FieldInitData {
	key: string;
	value: string;
	name: string;
	tooltip: string;
	label: string;
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
	 */
	set data({ key, value, name, tooltip, label }: FieldInitData) {
		const id = createId(key);

		value = value || '';
		tooltip = tooltip || '';
		label = label || createLabel(key);

		if (typeof value === 'string') {
			value = htmlSpecialChars(value);
		}

		this._data = {
			name,
			id,
			label,
			value,
			tooltip,
		};

		this.render();
	}

	/**
	 * Create a label.
	 */
	label(): void {
		const { id, label, tooltip } = this._data;
		const wrapper = create('div', [], {}, this);

		wrapper.innerHTML = html`
			<label class="${classes.fieldLabel}" for="${id}" title="${tooltip}">
				${label}
			</label>
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
