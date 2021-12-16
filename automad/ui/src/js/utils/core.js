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

export const dashboardURL = () => {
	if (typeof window.dashboard === 'undefined') {
		console.error(
			'The Dashboard URL is not defined. Please define "window.dashboard".'
		);

		return false;
	}

	return window.dashboard;
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
