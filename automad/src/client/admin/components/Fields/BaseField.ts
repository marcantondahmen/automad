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
	App,
	Attr,
	create,
	createIdFromField,
	createLabelFromField,
	CSS,
	htmlSpecialChars,
	query,
	queryAll,
	Undo,
} from '@/core';
import {
	FieldInitData,
	FieldRenderData,
	InputElement,
	KeyValueMap,
	UndoCapableField,
	UndoValue,
} from '@/types';
import { BaseComponent } from '@/components/Base';

/**
 * A standard input field with a label.
 *
 * Fields can have several attributes:
 * - `required` - with and empty value, a form can't be submitted
 * - `spellcheck` - enable spell checking
 *
 * @extends BaseComponent
 * @implements UndoCapableField
 */
export abstract class BaseFieldComponent
	extends BaseComponent
	implements UndoCapableField
{
	/**
	 * If true the field data is spell checked while editing.
	 */
	protected get isSpellchecked(): boolean {
		return this.hasAttribute('spellcheck');
	}

	/**
	 * Add "for" attribute to label.
	 */
	protected linkLabel = true;

	/**
	 * The internal field data.
	 */
	protected _data: FieldRenderData;

	/**
	 * Get the actual field input element.
	 */
	get input(): InputElement {
		return query('[name]', this);
	}

	/**
	 * The field data.
	 *
	 * @param params
	 * @param params.key
	 * @param params.value
	 * @param params.name
	 * @param params.id
	 * @param params.tooltip
	 * @param params.label
	 * @param params.placeholder
	 */
	set data({
		key,
		value,
		name,
		id,
		tooltip,
		label,
		placeholder,
	}: FieldInitData) {
		id = id ?? createIdFromField(key);
		value = typeof value === 'undefined' ? '' : value;
		tooltip = htmlSpecialChars(tooltip || '');
		label = label || createLabelFromField(key);
		placeholder = placeholder || '';

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
	 * Return the field that is observed for changes.
	 *
	 * @return the input field
	 */
	getValueProvider(): HTMLElement {
		return this.input;
	}

	/**
	 * A function that can be used to mutate the field value.
	 *
	 * @param value
	 */
	mutate(value: UndoValue): void {
		this.input.value = value;
	}

	/**
	 * Query the current field value.
	 *
	 * @return the current value
	 */
	query(): UndoValue {
		return this.input.value;
	}

	/**
	 * Render field when data is set.
	 */
	protected create(): void {
		this.createLabel();
		this.createInput();
		this.applyAttributes();

		Undo.attach(this);
	}

	/**
	 * Create a label.
	 */
	protected createLabel(): void {
		const { label, tooltip } = this._data;
		const attributes: KeyValueMap = {};

		if (this.linkLabel) {
			attributes['for'] = this._data.id;
		}

		const wrapper = create('div', [], {}, this);
		const element = create('label', [CSS.fieldLabel], attributes, wrapper);

		create('span', [], {}, element).textContent = label;

		if (tooltip) {
			create('i', ['bi', 'bi-lightbulb'], {}, element);

			element.setAttribute(Attr.tooltip, tooltip);
		}
	}

	/**
	 * Create an input field.
	 */
	protected abstract createInput(): void;

	/**
	 * Apply field attributes to actual input elements.
	 */
	private applyAttributes(): void {
		queryAll<InputElement>('input, textarea', this).forEach((input) => {
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

			[Attr.bind, Attr.bindTo, Attr.toggle, 'pattern'].forEach(
				(attribute) => {
					if (this.hasAttribute(attribute)) {
						input.setAttribute(
							attribute,
							this.getAttribute(attribute)
						);
						this.removeAttribute(attribute);
					}
				}
			);
		});
	}
}
