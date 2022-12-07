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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { create, fire, listen, queryAll } from '.';
import { InputElement, KeyValueMap } from '../types';
import { eventNames } from './events';

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
	 * The value getter.
	 */
	get value() {
		if (this.modifier) {
			return this.modifier(this.input.value);
		} else {
			return this.input.value;
		}
	}

	/**
	 * Set the input value.
	 */
	set value(value) {
		this.input.value = value;
		fire('input', this.input);
	}

	/**
	 * The constructor.
	 *
	 * @param name
	 * @param [input]
	 * @param [modifier]
	 * @param [initial]
	 */
	constructor(
		name: string,
		input: InputElement = null,
		modifier: Function = null,
		initial: any = null
	) {
		this.modifier = modifier;

		// In case no input is given, an empty input element will be create without
		// appending it to the DOM.
		// It will then just serve to store the value and trigger events.
		this.input = input || create('input', [], {});

		Bindings.add(name, this);

		if (initial !== null) {
			this.value = initial;
		}
	}
}

/**
 * The Bindings class connects elements to a data binding instance.
 *
 * @example
 * <span bind="title">Text</span>
 * <a bind="url" bindto="href" href="...">Text</a>
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
		queryAll('[bind]', container).forEach((element) => {
			const key = element.getAttribute('bind');
			const binding: Binding = this._bindings[key];

			if (!binding) {
				return;
			}

			const bindTo = element.getAttribute('bindto');
			let targetProperties: string[] = [];

			if (bindTo) {
				targetProperties = bindTo.split(' ');
				element.removeAttribute('bindto');
			} else {
				targetProperties = ['textContent'];
			}

			element.removeAttribute('bind');

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

					fire(eventNames.changeByBinding, element);
				});
			};

			listen(binding.input, 'input', update);
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
