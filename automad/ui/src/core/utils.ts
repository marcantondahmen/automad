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

import { KeyValueMap } from '../types';

declare global {
	interface Event {
		path: string[];
	}

	interface ParentNode {
		closest: any;
	}
}

/**
 * The object with all classes used for HTML elements that are used by components.
 */
export const classes: KeyValueMap = {
	alert: 'am-c-alert',
	alertDanger: 'am-c-alert--danger',
	alertSuccess: 'am-c-alert--success',
	alertIcon: 'am-c-alert__icon',
	alertText: 'am-c-alert__text',
	breadcrumbs: 'am-c-breadcrumbs',
	breadcrumbsItem: 'am-c-breadcrumbs__item',
	button: 'am-e-button',
	buttonSuccess: 'am-e-button--success',
	card: 'am-c-card',
	cardImage: 'am-c-card__image',
	cardTitle: 'am-c-card__title',
	cardBody: 'am-c-card__body',
	cardFooter: 'am-c-card__footer',
	displayNone: 'am-u-display-none',
	dropdownItems: 'am-c-dropdown__items',
	dropdownItemsFullWidth: 'am-c-dropdown__items--full-width',
	dropdownItem: 'am-c-dropdown__item',
	dropdownItemActive: 'am-c-dropdown__item--active',
	dropdown: 'am-c-dropdown',
	dropdownOpen: 'am-c-dropdown--open',
	dropdownForm: 'am-c-dropdown--form',
	field: 'am-c-field',
	fieldChanged: 'am-c-field--changed',
	fieldLabel: 'am-c-field__label',
	flex: 'am-u-flex',
	flexItemGrow: 'am-u-flex__item-grow',
	grid: 'am-c-grid',
	input: 'am-e-input',
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
	overflowHidden: 'am-u-overflow-hidden',
	root: 'am-c-root',
	rootLoading: 'am-c-root--loading',
	switcherLinkActive: 'am-c-switcher-link--active',
	upload: 'am-c-upload',
	uploadDropzone: 'am-c-upload__dropzone',
	uploadWindow: 'am-c-upload__window',
	uploadWindowOpen: 'am-c-upload__window--open',
	uploadWindowGrip: 'am-c-upload__window-grip',
	uploadPreviews: 'am-c-upload__previews',
};

/**
 * Debounce a function.
 *
 * @param callback
 * @param timeout
 * @returns the debounced function
 */
export const debounce = (
	callback: Function,
	timeout: number = 50
): Function => {
	let timer: NodeJS.Timer;

	return (...args: any) => {
		clearTimeout(timer);

		timer = setTimeout(() => {
			callback.apply(this, args);
		}, timeout);
	};
};

/**
 * Fires an event on an element or the window.
 * @param name
 * @param [element]
 */
export const fire = (
	name: string,
	element: HTMLElement | Document | Window = window
): void => {
	element.dispatchEvent(new Event(name));
};

/**
 * Get the current page URL from the query string.
 *
 * @returns a page URL
 */
export const getPageURL = (): string => {
	const searchParams = new URLSearchParams(window.location.search);

	return searchParams.get('url');
};

/**
 * Handle the rendering of template literals and optionally escape values
 * that are preceeded with a `$`.
 *
 * @example
 * return html`
 *     <p>${ value }</p>
 *     <p>$${ escapedValue }</p>
 * `;
 *
 * @see {@link 2ality https://2ality.com/2015/01/template-strings-html.html#the-template-handler}
 * @param strings
 * @param values
 * @returns the rendered template
 */
export const html = (strings: any, ...values: any[]): string => {
	let raw = strings.raw;

	let result = '';

	values.forEach((value, i) => {
		let section = raw[i];

		if (section.endsWith('$')) {
			value = htmlSpecialChars(value);
			section = section.slice(0, -1);
		}

		result += section + value;
	});

	result += raw[raw.length - 1];

	return result;
};

/**
 * Convert all HTML special characters.
 *
 * @param value
 * @returns the converted string
 */
export const htmlSpecialChars = (value: string): string => {
	const chars: KeyValueMap = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#039;',
	};

	return value.replace(/[&<>"']/g, (char: string) => {
		return chars[char];
	});
};

/**
 * Test whether a slug is active.
 *
 * @param slug
 * @returns true if the slug mathes the URL path
 */
export const isActivePage = (slug: string): boolean => {
	const regex = new RegExp(`\/${slug}\$`, 'i');
	return window.location.pathname.match(regex) != null;
};

/**
 * Register a keycombo.
 *
 * @param key
 * @param callback
 */
export const keyCombo = (key: string, callback: Function): void => {
	listen(window, 'keydown', (event: KeyboardEvent) => {
		if (event.ctrlKey || event.metaKey) {
			const _key: string = String.fromCharCode(event.which).toLowerCase();

			if (key == _key) {
				event.preventDefault();
				callback.apply(event.target, [event]);
				return;
			}
		}
	});
};

/**
 * Register event listeners.
 *
 * @param element - the element to register the event listeners to
 * @param eventNamesString - a string of one or more event names separated by a space
 * @param callback - the callback
 * @param selector - the sector to be used as filter
 */
export const listen = (
	element: HTMLElement | Document | Window,
	eventNamesString: string,
	callback: Function,
	selector: string = ''
): void => {
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

			path.forEach((_element: any) => {
				try {
					if (_element.matches(selector)) {
						callback.apply(event.target, [event]);
						return;
					}
				} catch (error) {}
			});
		});
	});
};

/**
 * Query the first element matching a `selector` from another `element`.
 *
 * @param selector
 * @param [element] - optional, defaults to `document`
 * @returns the first matched element
 */
export const query = (
	selector: string,
	element: Document | HTMLElement = document
): HTMLElement => {
	return element.querySelector(selector);
};

/**
 * Query an array of elements matching a `selector` from another `element`.
 *
 * @param selector
 * @param [element] - optional, defaults to `document`
 * @returns an array of matched elements
 */
export const queryAll = (
	selector: string,
	element: HTMLElement | Document = document
): HTMLElement[] => {
	return Array.from(element.querySelectorAll(selector));
};

/**
 * Query an array of parents of a given `element` that are matching `selector`.
 *
 * @param selector
 * @param element
 * @returns an array of matched parent elements
 */
export const queryParents = (
	selector: string,
	element: HTMLElement
): HTMLElement[] => {
	const parents: HTMLElement[] = [];
	let parent = element.closest(selector) as HTMLElement;

	while (parent !== null) {
		parents.push(parent);
		parent = parent.parentNode.closest(selector);
	}

	return parents;
};

/**
 * Set the document title.
 *
 * @param page
 */
export const setDocumentTitle = (page: string): void => {
	document.title = `Automad — ${htmlSpecialChars(page)}`;
};

/**
 * Title case a given string.
 *
 * @param str
 * @returns the converted string
 */
export const titleCase = (str: string): string => {
	return str
		.replace(/\//g, ' / ')
		.replace(/(?!^)([A-Z]+)/g, ' $1')
		.replace('_', ' ')
		.split(' ')
		.map((word) => word.charAt(0).toUpperCase() + word.slice(1))
		.join(' ');
};
