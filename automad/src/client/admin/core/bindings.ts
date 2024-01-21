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
 * Copyright (c) 2022-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { Attr, create, EventName, fire, listen, queryAll } from '.';
import { BindingOptions, InputElement, KeyValueMap } from '@/types';

/**
 * A data binding class that allows to bind an input node to a value modifier function.
 */
export class Binding {
	/**
	 * The input element.
	 */
	input: InputElement;

	/**
	 * The modifier function that can optionally be defined to compute the value.
	 */
	private modifier: Function;

	/**
	 * The onChange handler.
	 */
	private onChange: Function;

	/**
	 * Set this to true in case there is no input passed in the options and the value
	 * possibly is boolean or number.
	 */
	private storeValueAsObject: boolean;

	/**
	 * The value getter.
	 */
	get value() {
		let value = this.input.value;

		if (this.storeValueAsObject) {
			// Get value from JSON string in order to corecctly handle booleans and numbers.
			if (typeof value != 'string' || value != '') {
				const data = JSON.parse(value);
				value = data.value;
			}
		}

		if (this.modifier) {
			return this.modifier(value);
		} else {
			return value;
		}
	}

	/**
	 * Set the input value.
	 */
	set value(value) {
		if (this.storeValueAsObject) {
			this.input.value = JSON.stringify({ value });
		} else {
			this.input.value = value;
		}

		if (this.onChange) {
			this.onChange(value);
		}

		fire('input', this.input);
	}

	/**
	 * The constructor.
	 *
	 * @param name
	 * @param [options]
	 */
	constructor(name: string, options: BindingOptions = {}) {
		options = Object.assign(
			{ initial: null, input: null, modifier: null, onChange: null },
			options
		);

		this.modifier = options.modifier;
		this.storeValueAsObject = options.input === null;
		this.onChange = options.onChange;

		// In case no input is given, an empty input element will be create without
		// appending it to the DOM.
		// It will then just serve to store the value and trigger events.
		this.input = options.input || create('input', [], {});

		Bindings.add(name, this);

		if (options.initial !== null) {
			this.value = options.initial;
		}
	}
}

/**
 * The Bindings class connects elements to a data binding instance.
 *
 * @example
 * <span ${Attr.bind}="title">Text</span>
 * <a ${Attr.bind}="url" ${Attr.bindTo}="href" href="...">Text</a>
 */
export class Bindings {
	/**
	 * The bindings collection.
	 *
	 * @static
	 */
	private static _bindings: KeyValueMap = {};

	/**
	 * Add a binding instance.
	 *
	 * @param name
	 * @param binding
	 * @static
	 */
	static add(name: string, binding: Binding) {
		this._bindings[name] = binding;
	}

	/**
	 * Delete a binding by name.
	 *
	 * @param name
	 * @static
	 */
	static delete(name: string): void {
		delete this._bindings[name];
	}

	/**
	 * Get a binding by name.
	 *
	 * @param name
	 * @returns the binding
	 * @static
	 */
	static get(name: string) {
		return this._bindings[name];
	}

	/**
	 * Connect all elements that have a bind attribute to the actual binding instance.
	 *
	 * @param container
	 * @static
	 */
	static connectElements(container: HTMLElement): void {
		queryAll(`[${Attr.bind}]`, container).forEach((element) => {
			const key = element.getAttribute(Attr.bind);
			const binding: Binding = this._bindings[key];

			if (!binding) {
				return;
			}

			const bindTo = element.getAttribute(Attr.bindTo);
			let targetProperties: string[] = [];

			if (bindTo) {
				targetProperties = bindTo.split(' ');
				element.removeAttribute(Attr.bindTo);
			} else {
				targetProperties = ['textContent'];
			}

			element.removeAttribute(Attr.bind);

			const update = () => {
				targetProperties.forEach((prop) => {
					switch (prop) {
						case 'textContent':
							element.textContent = binding.value;
							break;
						case 'checked':
							(element as HTMLInputElement).checked =
								binding.value;
							break;
						case 'disabled':
							if (binding.value) {
								element.setAttribute('disabled', '');
							} else {
								element.removeAttribute('disabled');
							}
							break;
						case 'value':
							(element as InputElement).value = binding.value;
							break;
						default:
							element.setAttribute(prop, binding.value);
					}

					fire(EventName.changeByBinding, element);
				});
			};

			listen(binding.input, 'change input', update);
			fire('input', binding.input);
		});
	}

	/**
	 * Remove all previously added bindings
	 *
	 * @static
	 */
	static reset(): void {
		this._bindings = {};
	}
}
