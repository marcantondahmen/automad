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

/**
 * Create a new element including class names and attributes and optionally append it to a given parent node.
 *
 * @param tag - the tag name
 * @param classes - an array of class names that are added to the element
 * @param attributes - an object of attributes (key/value pairs) that are added to the element
 * @param [parent] - the optional node where the element will be appendend to
 * @param [innerHTML] - the optional innerHTML of the created element
 * @param [prepend] - prepend instead of append
 * @returns the created element
 */
export const create = (
	tag: string,
	classes: string[] = [],
	attributes: object = {},
	parent: HTMLElement | null = null,
	innerHTML: string = null,
	prepend: boolean = false
): any => {
	const element = document.createElement(tag);

	classes.forEach((cls) => {
		element.classList.add(cls);
	});

	for (const [key, value] of Object.entries(attributes)) {
		element.setAttribute(key, value);
	}

	if (parent) {
		if (prepend) {
			parent.prepend(element);
		} else {
			parent.appendChild(element);
		}
	}

	if (innerHTML) {
		element.innerHTML = innerHTML;
	}

	return element;
};

/**
 * Debounce a function.
 *
 * @param callback
 * @param [timeout]
 * @returns the debounced function
 */
export const debounce = (
	callback: (...args: any[]) => void,
	timeout: number = 50
): ((...args: any[]) => void) => {
	let timer: NodeJS.Timer;

	return (...args: any[]) => {
		clearTimeout(timer);

		timer = setTimeout(() => {
			callback.apply(this, args);
		}, timeout);
	};
};

/**
 * Query the first element matching a `selector` from another `element`.
 *
 * @param selector
 * @param [element] - optional, defaults to `document`
 * @returns the first matched element
 */
export const query = <T extends HTMLElement = HTMLElement>(
	selector: string,
	element: Document | HTMLElement = document
): T => {
	return element.querySelector(selector);
};

/**
 * Query an array of elements matching a `selector` from another `element`.
 *
 * @param selector
 * @param [element] - optional, defaults to `document`
 * @returns an array of matched elements
 */
export const queryAll = <T extends HTMLElement = HTMLElement>(
	selector: string,
	element: HTMLElement | Document = document
): T[] => {
	return Array.from(element.querySelectorAll(selector));
};

/**
 * Query an array of parents of a given `element` that are matching `selector`.
 *
 * @param selector
 * @param element
 * @returns an array of matched parent elements
 */
export const queryParents = <T extends HTMLElement = HTMLElement>(
	selector: string,
	element: HTMLElement
): T[] => {
	const parents: T[] = [];
	let parent = element?.closest<T>(selector) || null;

	while (parent !== null) {
		parents.push(parent);
		parent = parent.parentNode.closest(selector);
	}

	return parents;
};
