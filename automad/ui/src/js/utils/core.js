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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

export const getBaseURL = () => {
	if (
		typeof window.Automad === 'undefined' ||
		typeof window.Automad.baseURL === 'undefined'
	) {
		console.error(
			'The base URL is not defined. Please define "window.Automad.baseURL".'
		);

		return false;
	}

	return window.Automad.baseURL;
};

export const getDashboardURL = () => {
	if (
		typeof window.Automad === 'undefined' ||
		typeof window.Automad.dashboardURL === 'undefined'
	) {
		console.error(
			'The Dashboard URL is not defined. Please define "window.Automad.dashboardURL".'
		);

		return false;
	}

	return window.Automad.dashboardURL;
};

export const getSwitcherSections = () => {
	if (
		typeof window.Automad === 'undefined' ||
		typeof window.Automad.sections === 'undefined'
	) {
		console.error(
			'The sections object is not defined. Please define "window.Automad.sections".'
		);

		return false;
	}

	return window.Automad.sections;
};

export const debounce = (callback, timeout = 50) => {
	let timer;

	return (...args) => {
		clearTimeout(timer);

		timer = setTimeout(() => {
			callback.apply(this, args);
		}, timeout);
	};
};

export const htmlSpecialChars = (value) => {
	const chars = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#039;',
	};

	return value.replace(/[&<>"']/g, (char) => {
		return chars[char];
	});
};

export const listen = (element, eventNamesString, callback, selector = '') => {
	const eventNames = eventNamesString
		.split(' ')
		.filter((str) => str.length > 0);

	eventNames.forEach((eventName) => {
		element.addEventListener(eventName, function (event) {
			if (!selector) {
				callback.apply(event.target, [event]);
				return;
			}

			const path =
				event.path || (event.composedPath && event.composedPath());

			path.forEach((_element) => {
				try {
					if (_element.matches(selector)) {
						if (typeof callback === 'function') {
							callback.apply(event.target, [event]);
							return;
						}
					}
				} catch (error) {}
			});
		});
	});
};

export const query = (selector, element = document) => {
	return element.querySelector(selector);
};

export const queryAll = (selector, element = document) => {
	return Array.from(element.querySelectorAll(selector));
};

export const queryParents = (selector, element) => {
	const parents = [];
	let parent = element.closest(selector);

	while (parent !== null) {
		parents.push(parent);
		parent = parent.parentNode.closest(selector);
	}

	return parents;
};

export const text = (key) => {
	try {
		return window.Automad.textModules[key];
	} catch (error) {
		console.log(error);
	}
};

export const titleCase = (str) => {
	return str
		.replace(/\//g, ' / ')
		.replace(/(?!^)([A-Z]+)/g, ' $1')
		.replace('_', ' ')
		.split(' ')
		.map((word) => word.charAt(0).toUpperCase() + word.slice(1))
		.join(' ');
};
