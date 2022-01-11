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

/**
 * The object with all classes used for HTML elements that are used by components.
 *
 * @type {Object}
 */
export const classes = {
	button: 'am-e-button',
	buttonSuccess: 'am-e-button--success',
	hidden: 'am-u-display-none',
	overflowHidden: 'am-u-overflow-hidden',
	dropdownItem: 'am-c-dropdown__item',
	dropdownItemActive: 'am-c-dropdown__item--active',
	dropdown: 'am-c-dropdown',
	field: 'am-c-field',
	fieldChanged: 'am-c-field--changed',
	fieldLabel: 'am-c-field__label',
	input: 'am-e-input',
	inputLarge: 'am-e-input--large',
	inputTitle: 'am-e-input--title',
	muted: 'am-u-text-muted',
	modal: 'am-c-modal',
	modalOpen: 'am-c-modal--open',
	modalDialog: 'am-c-modal__dialog',
	modalHeader: 'am-c-modal__header',
	modalClose: 'am-c-modal__close',
	modalFooter: 'am-c-modal__footer',
	nav: 'am-c-nav',
	navItem: 'am-c-nav__item',
	navItemActive: 'am-c-nav__item--active',
	navLabel: 'am-c-nav__label',
	navLink: 'am-c-nav__link',
	navLinkHasChildren: 'am-c-nav__link--has-children',
	navChildren: 'am-c-nav__children',
	navSpinner: 'am-c-nav__spinner',
	spinner: 'am-e-spinner',
	switcherLinkActive: 'am-c-switcher-link--active',
};

/**
 * Get the Automad base URL.
 *
 * @returns {string} the Automad base URL
 */
export const getBaseURL = () => {
	try {
		return window.Automad.baseURL;
	} catch {
		console.error('window.Automad.baseURL is not defined.');
	}
};

/**
 * Get the Automad dashboard URL.
 *
 * @returns {string} the Automad dashboard URL
 */
export const getDashboardURL = () => {
	try {
		return window.Automad.dashboardURL;
	} catch {
		console.error('window.Automad.dashboardURL is not defined.');
	}
};

/**
 * Get the array of globally used tags.
 *
 * @returns {Array} the array of globally used tags
 */
export const getTags = () => {
	try {
		return window.Automad.tags;
	} catch {
		console.error('window.Automad.tags is not defined.');
	}
};

/**
 * Get the installed themes.
 *
 * @returns {Object} the installed themes
 */
export const getThemes = () => {
	try {
		return window.Automad.themes;
	} catch {
		console.error('window.Automad.themes is not defined.');
	}
};

/**
 * Get the available switcher sections names.
 *
 * @returns {Object} the available switcher sections names
 */
export const getSwitcherSections = () => {
	try {
		return window.Automad.sections;
	} catch {
		console.error('window.Automad.sections is not defined.');
	}
};

/**
 * Debounce a function.
 *
 * @param {function} callback
 * @param {number} timeout
 * @returns {function} the debounced function
 */
export const debounce = (callback, timeout = 50) => {
	let timer;

	return (...args) => {
		clearTimeout(timer);

		timer = setTimeout(() => {
			callback.apply(this, args);
		}, timeout);
	};
};

/**
 * Convert all HTML special characters.
 *
 * @param {string} value
 * @returns {string} the converted string
 */
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

/**
 * Register event listeners.
 *
 * @param {HTMLElement} element - the element to register the event listeners to
 * @param {string} eventNamesString - a string of one or more event names separated by a space
 * @param {function} callback - the callback
 * @param {string} selector - the sector to be used as filter
 */
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

/**
 * Query the first element matching a `selector` from another `element`.
 *
 * @param {string} selector
 * @param {HTMLElement} [element] - optional, defaults to `document`
 * @returns {(HTMLElement|null)}
 */
export const query = (selector, element = document) => {
	return element.querySelector(selector);
};

/**
 * Query an array of elements matching a `selector` from another `element`.
 *
 * @param {string} selector
 * @param {HTMLElement} [element] - optional, defaults to `document`
 * @returns {Array}
 */
export const queryAll = (selector, element = document) => {
	return Array.from(element.querySelectorAll(selector));
};

/**
 * Query an array of parents of a given `element` that are matching `selector`.
 *
 * @param {string} selector
 * @param {HTMLElement} element
 * @returns {Array}
 */
export const queryParents = (selector, element) => {
	const parents = [];
	let parent = element.closest(selector);

	while (parent !== null) {
		parents.push(parent);
		parent = parent.parentNode.closest(selector);
	}

	return parents;
};

/**
 * Get a text module by key.
 *
 * @param {string} key
 * @returns {string}
 */
export const text = (key) => {
	try {
		return window.Automad.text[key];
	} catch (error) {
		console.error(error);
	}
};

/**
 * Title case a given string.
 *
 * @param {string} str
 * @returns {string}
 */
export const titleCase = (str) => {
	return str
		.replace(/\//g, ' / ')
		.replace(/(?!^)([A-Z]+)/g, ' $1')
		.replace('_', ' ')
		.split(' ')
		.map((word) => word.charAt(0).toUpperCase() + word.slice(1))
		.join(' ');
};
