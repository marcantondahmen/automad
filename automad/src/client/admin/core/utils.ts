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

import { App, classes, create, listen } from '.';
import { KeyValueMap, Listener } from '../types';

declare global {
	interface Event {
		path: string[];
	}

	interface ParentNode {
		closest: any;
	}
}

/**
 * A simple confirmation modal.
 *
 * @param text
 * @async
 */
export const confirm = async (text: string): Promise<any> => {
	let modal = create(
		'am-modal',
		[],
		{ destroy: '', noclick: '', noesc: '' },
		App.root
	);

	modal.innerHTML = html`
		<div class="${classes.modalDialog}">
			<am-icon-text
				icon="exclamation-circle"
				text="$${text}"
			></am-icon-text>
			<div class="${classes.modalFooter}">
				<am-modal-close class="${classes.button}">
					<am-icon-text
						icon="x"
						text="$${App.text('cancel')}"
					></am-icon-text>
				</am-modal-close>
				<am-modal-close
					confirm
					class="${classes.button} ${classes.buttonPrimary}"
				>
					<am-icon-text
						icon="check"
						text="$${App.text('ok')}"
					></am-icon-text>
				</am-modal-close>
			</div>
		</div>
	`;

	setTimeout(() => {
		modal.open();
	}, 0);

	return new Promise((resolve, reject) => {
		const execute = (event: Event) => {
			let isConfirmed = false;

			modal.close();
			modal = null;

			try {
				const element = event.target as HTMLElement;

				isConfirmed =
					element.matches('[confirm]') ||
					element.closest('[confirm]') != null;
			} catch {}

			resolve(isConfirmed);
		};

		listen(modal, 'click', execute, 'am-modal-close');
	});
};

/**
 * Create an ID from a field key.
 *
 * @param key
 * @returns the generated ID
 */
export const createIdFromField = (key: string): string => {
	return `am-id-field__${key.replace(/(?!^)([A-Z])/g, '-$1').toLowerCase()}`;
};

/**
 * Create a label text from a field key.
 *
 * @param key
 * @returns the generated label
 */
export const createLabelFromField = (key: string): string => {
	return titleCase(key.replace('+', ''))
		.replace('Color ', '')
		.replace('Checkbox ', '');
};

/**
 * Debounce a function.
 *
 * @param callback
 * @param [timeout]
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
 * @return the listener
 */
export const keyCombo = (key: string, callback: Function): Listener => {
	return listen(window, 'keydown', (event: KeyboardEvent) => {
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
	document.title = `${htmlSpecialChars(page)} — Automad`;
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
