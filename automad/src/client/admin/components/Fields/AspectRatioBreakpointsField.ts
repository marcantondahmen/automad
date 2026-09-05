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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import {
	App,
	Attr,
	CSS,
	FieldTag,
	FormDataProviders,
	aspectRatioBreakpointsFromString,
	aspectRatioBreakpointsToString,
	create,
	fire,
	query,
	type AspectRatioBreakpoints,
	type UndoValue,
} from '@/admin/core';
import { BaseFieldComponent } from './BaseField';

/**
 * A aspect ratio breakpoints input component.
 *
 * @extends BaseFieldComponent
 */
export class AspectRatioBreakpointsFieldComponent extends BaseFieldComponent {
	/**
	 * The field value.
	 */
	private _value: AspectRatioBreakpoints = {};

	get value(): AspectRatioBreakpoints {
		return this._value;
	}

	set value(value: AspectRatioBreakpoints) {
		const input = query<HTMLInputElement>('input', this);

		if (input) {
			input.value = aspectRatioBreakpointsToString(value);
		}

		this._value = value;
	}

	/**
	 * Render the field.
	 */
	protected createInput(): void {
		const { name, value: aspectRatioBreakpoints } = this._data as {
			name: string;
			value: AspectRatioBreakpoints;
		};

		this.setAttribute('name', name);
		this.setAttribute(Attr.error, App.text('aspectRatioBreakpointsError'));

		const input = create(
			'input',
			[CSS.input],
			{
				type: 'text',
				placeholder: '600:1/1 900:2/3',
				pattern: '([1-9][0-9]+:[0-9.]+/[0-9.]+( |$))*',
				value: aspectRatioBreakpointsToString(aspectRatioBreakpoints),
			},
			this
		);

		this.listen(input, 'input', () => {
			this._value = aspectRatioBreakpointsFromString(input.value);

			fire('change', this);
		});
	}

	/**
	 * This field has no label.
	 */
	protected createLabel(): void {}

	/**
	 * Return the field that is observed for changes.
	 *
	 * @return the input field
	 */
	getValueProvider(): HTMLElement {
		return this;
	}

	/**
	 * A function that can be used to mutate the field value.
	 *
	 * @param value
	 */
	async mutate(value: UndoValue): Promise<void> {
		this.value = value;
	}

	/**
	 * Query the current field value.
	 *
	 * @return the current value
	 */
	query() {
		return this.value;
	}
}

FormDataProviders.add(FieldTag.aspectRatioBreakpoints);
customElements.define(
	FieldTag.aspectRatioBreakpoints,
	AspectRatioBreakpointsFieldComponent
);
