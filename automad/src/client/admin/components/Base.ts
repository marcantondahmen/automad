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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { KeyValueMap, Listener } from '@/admin/types';
import { listen } from '../core';

/**
 * The Automad base component. All Automad components are based on this class.
 *
 * @extends HTMLElement
 */
export abstract class BaseComponent extends HTMLElement {
	/**
	 * Key/value pairs of the element attributes.
	 */
	elementAttributes: KeyValueMap = {};

	/**
	 * The array of event listeners that have to be remove on destruction.
	 */
	protected listeners: Listener[] = [];

	/**
	 * The class constructor.
	 */
	constructor() {
		super();
	}

	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return [];
	}

	/**
	 * The callback that is used when attributes are changed or on initialization.
	 *
	 * @param name
	 * @param oldValue
	 * @param newValue
	 */
	attributeChangedCallback(
		name: string,
		oldValue: string,
		newValue: string
	): void {
		this.elementAttributes[name] = newValue || '';
	}

	/**
	 * Remove all window event listeners when disconnecting.
	 */
	disconnectedCallback(): void {
		this.removeListeners();
	}

	/**
	 * Add a listener that will be removed on destruction.
	 *
	 * @param listener
	 */
	protected addListener(listener: Listener): void {
		this.listeners.push(listener);
	}

	/**
	 * Create a listener that will be removed on destruction.
	 *
	 * @param element - the element to register the event listeners to
	 * @param eventNamesString - a string of one or more event names separated by a space
	 * @param callback - the callback
	 * @param [selector] - the sector to be used as filter
	 */
	protected listen(
		element: HTMLElement | Document | Window,
		eventNamesString: string,
		callback: (event: Event) => void,
		selector: string = ''
	): void {
		this.addListener(listen(element, eventNamesString, callback, selector));
	}

	/**
	 * Remove all listeners that have been added using addListener.
	 */
	protected removeListeners(): void {
		this.listeners.forEach((listener) => {
			listener.remove();
		});
	}
}
