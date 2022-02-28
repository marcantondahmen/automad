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

import { fire, listen, queryAll } from '.';
import { KeyValueMap } from '../types';
const bindingChangedEventName = 'AutomadBindingChange';

/**
 * A data binding class that allows to bind an input node to a value modifier function.
 */
export class Binding {
	/**
	 * The input element.
	 */
	input: HTMLInputElement;

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
	 * The constructor.
	 *
	 * @param name
	 * @param input
	 * @param modifier
	 */
	constructor(
		name: string,
		input: HTMLInputElement,
		modifier: Function = null
	) {
		this.modifier = modifier;
		this.input = input;

		Bindings.add(name, this);
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
					if (prop == 'textContent') {
						element.textContent = binding.value;
					} else {
						element.setAttribute(prop, binding.value);
					}
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
