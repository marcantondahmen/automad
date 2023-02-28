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

import { create, CSS, fire, FormDataProviders, listen } from '../core';
import { BaseComponent } from './Base';
import { SelectComponent } from './Select';

/**
 * A special input that combines a number input with a unit dropdown.
 *
 * @example
 * <am-number-unit-input name="..." value="2rem"></am-number-unit-input>
 *
 * @extends BaseComponent
 */
class NumberUnitInputComponent extends BaseComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 */
	static TAG_NAME = 'am-number-unit-input';

	/**
	 * The combined value as string.
	 */
	value: string;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.value = this.getAttribute('value');
		this.removeAttribute('value');

		this.render(this.value);
	}

	/**
	 * Render the input combo.
	 */
	private render(value: string): void {
		this.classList.add(CSS.formGroup, CSS.numberUnit);
		const number = value.replace(/([^\d\.]+)/g, '');
		const unit = value.replace(/.+?(px|em|rem|%|vh|vw)/g, '$1') || 'px';

		const numberInput = create(
			'input',
			[CSS.input, CSS.formGroupItem],
			{
				type: 'number',
				step: '0.1',
				value: number,
			},
			this
		);

		const unitSelect = SelectComponent.create(
			[
				{ value: 'px' },
				{ value: '%' },
				{ value: 'rem' },
				{ value: 'em' },
				{ value: 'vh' },
				{ value: 'vw' },
			],
			unit,
			this,
			null,
			null,
			'',
			[CSS.formGroupItem]
		);

		const merge = (event: Event) => {
			event.stopPropagation();

			this.value =
				numberInput.value.length > 0
					? numberInput.value + unitSelect.value
					: '';

			fire('change', this);
		};

		listen(this, 'change', merge, 'input, select');
	}
}

FormDataProviders.add(NumberUnitInputComponent.TAG_NAME);
customElements.define('am-number-unit-input', NumberUnitInputComponent);
