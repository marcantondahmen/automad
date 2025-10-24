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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { Attr, create, EventName, fire, getLogger, listen, queryAll } from '.';
import {
	BindingOptions,
	InputElement,
	KeyValueMap,
	Listener,
} from '@/admin/types';

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
	private modifier: (value: any) => any;

	/**
	 * The onChange handler.
	 */
	private onChange: (value: any) => any;

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
 * Validate binding. Check if binding names used in "am-bind" actually exist
 * and if existing bindings are actually used in "am-bind" attributes.
 */
class BindingValidator {
	/**
	 * A unique array of bindings that are used in "am-bind" but are not found.
	 *
	 * @static
	 */
	private static missing: string[] = [];

	/**
	 * A unique array of binding names that have been actually created but are unused.
	 *
	 * @static
	 */
	private static unused: string[] = [];

	/**
	 * A unique, dynamically growing list of binding names that used in "am-bind" attributes.
	 *
	 * @static
	 */
	private static references: string[] = [];

	/**
	 * A unique, dynamically growing list of binding names that have been created.
	 *
	 * @static
	 */
	private static bindings: string[] = [];

	/**
	 * Track the missing binding references.
	 * Some bindings might be missing at first, but will be created after navigating to other pages that actually use them.
	 *
	 * Note that it is important to actually take a look at the console for a longer time navigating around.
	 *
	 * @param name
	 * @param binding
	 */
	static track(name: string, binding: Binding): void {
		if (!DEVELOPMENT) {
			return;
		}

		const log = getLogger();

		const currentMissing = [...this.missing];
		const currentUnused = [...this.unused];

		this.references = Array.from(new Set([...this.references, name]));
		this.bindings = Array.from(
			new Set([...this.bindings, ...Bindings.getBindingNames()])
		);

		if (!binding) {
			if (!this.bindings.includes(name)) {
				this.missing = Array.from(new Set([...this.missing, name]));
			}
		} else {
			this.missing = this.missing.filter((item) => item != name);
		}

		this.unused = this.bindings.filter(
			(binding: string) => !this.references.includes(binding)
		);

		if (JSON.stringify(currentMissing) != JSON.stringify(this.missing)) {
			if (this.missing.length > 0) {
				log.bindingInfo('Missing bindings', this.missing);
			} else {
				log.bindingSuccess('No missing bindings found, all ok!');
			}
		}

		if (JSON.stringify(currentUnused) != JSON.stringify(this.unused)) {
			if (this.unused.length > 0) {
				log.bindingInfo('Unused bindings', this.unused);
			} else {
				log.bindingSuccess('No unused bindings, all ok!');
			}
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
	private static bindings: KeyValueMap = {};

	/**
	 * The listener collection.
	 *
	 * @static
	 */
	private static listeners: Listener[] = [];

	/**
	 * Add a binding instance.
	 *
	 * @param name
	 * @param binding
	 * @static
	 */
	static add(name: string, binding: Binding) {
		this.bindings[name] = binding;
	}

	/**
	 * Delete a binding by name.
	 *
	 * @param name
	 * @static
	 */
	static delete(name: string): void {
		delete this.bindings[name];
	}

	/**
	 * Get a binding by name.
	 *
	 * @param name
	 * @returns the binding
	 * @static
	 */
	static get(name: string): Binding {
		BindingValidator.track(name, this.bindings[name]);

		return this.bindings[name];
	}

	/**
	 * The list of registered binding names.
	 *
	 * @return the list of binding names
	 * @static
	 */
	static getBindingNames(): string[] {
		return Object.keys(this.bindings);
	}

	/**
	 * Connect all elements that have the "am-bind" attribute to the actual binding instance.
	 *
	 * @param container
	 * @static
	 */
	static connectElements(container: HTMLElement): void {
		queryAll(`[${Attr.bind}]`, container).forEach((element) => {
			const key = element.getAttribute(Attr.bind);
			const binding: Binding = this.bindings[key];

			BindingValidator.track(key, binding);

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

			this.listeners.push(listen(binding.input, 'change input', update));

			fire('input', binding.input);
		});
	}

	/**
	 * Remove all previously added bindings
	 *
	 * @static
	 */
	static reset(): void {
		this.bindings = {};

		this.listeners.forEach((listener) => {
			listener.remove();
		});

		this.listeners = [];
	}
}
