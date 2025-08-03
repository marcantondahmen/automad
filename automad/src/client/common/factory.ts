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
export const create = <T extends any = any>(
	tag: string,
	classes: string[] = [],
	attributes: object = {},
	parent: HTMLElement | null = null,
	innerHTML: string = null,
	prepend: boolean = false
): T => {
	const element = document.createElement(tag);

	classes.forEach((cls) => {
		element.classList.add(cls);
	});

	for (const [key, value] of Object.entries(attributes)) {
		if (typeof value !== 'undefined') {
			element.setAttribute(key, value);
		}
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

	return element as T;
};
