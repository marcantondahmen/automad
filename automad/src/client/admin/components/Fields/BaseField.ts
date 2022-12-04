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
	App,
	create,
	createIdFromField,
	createLabelFromField,
	CSS,
	html,
	htmlSpecialChars,
	query,
	queryAll,
} from '../../core';
import { FieldInitData, FieldRenderData, InputElement } from '../../types';
import { BaseComponent } from '../Base';

/**
 * A standard input field with a label.
 *
 * Fields can have several attributes:
 * - `required` - with and empty value, a form can't be submitted
 * - `spellcheck` - enable spell checking
 *
 * @extends BaseComponent
 */
export abstract class BaseFieldComponent extends BaseComponent {
	/**
	 * If true the field data is sanitized.
	 */
	protected sanitize = true;

	/**
	 * If true the field data is spell checked while editing.
	 */
	protected get isSpellchecked(): boolean {
		return this.hasAttribute('spellcheck');
	}

	/**
	 * The internal field data.
	 */
	protected _data: FieldRenderData;

	/**
	 * Get the actual field input element.
	 */
	get input(): InputElement {
		return query('[name]', this) as InputElement;
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
	 * @param params.placeholder
	 */
	set data({ key, value, name, tooltip, label, placeholder }: FieldInitData) {
		const id = createIdFromField(key);

		value = value || '';
		tooltip = tooltip || '';
		label = label || createLabelFromField(key);
		placeholder = placeholder || '';

		if (typeof value === 'string' && this.sanitize) {
			value = htmlSpecialChars(value);
		}

		this._data = {
			name,
			id,
			label,
			value,
			tooltip,
			placeholder,
		};

		this.create();
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.field);
	}

	/**
	 * Render field when data is set.
	 */
	protected create(): void {
		this.createLabel();
		this.createInput();
		this.applyAttributes();
	}

	/**
	 * Create a label.
	 */
	protected createLabel(): void {
		const { id, label, tooltip } = this._data;
		const wrapper = create('div', [], {}, this);

		wrapper.innerHTML = html`
			<label class="${CSS.fieldLabel}" for="${id}" title="${tooltip}">
				${label}
			</label>
		`;
	}

	/**
	 * Create an input field.
	 */
	protected abstract createInput(): void;

	/**
	 * Apply field attributes to actual input elements.
	 */
	private applyAttributes(): void {
		queryAll('input, textarea', this).forEach((input: InputElement) => {
			if (this.hasAttribute('required')) {
				input.setAttribute('pattern', '.*\\S.*');
				input.setAttribute('placeholder', App.text('requiredField'));
				input.setAttribute('required', '');
				this.removeAttribute('required');
			}

			input.setAttribute(
				'spellcheck',
				this.isSpellchecked ? 'true' : 'false'
			);

			['bind', 'bindto', 'toggle'].forEach((attr) => {
				if (this.hasAttribute(attr)) {
					input.setAttribute(attr, this.getAttribute(attr));
					this.removeAttribute(attr);
				}
			});
		});
	}
}
