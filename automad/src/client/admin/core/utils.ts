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
 * Copyright (c) 2021-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, CSS, create, html, listen } from '.';
import { Listener } from '../types';

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
		{ [Attr.destroy]: '', [Attr.noClick]: '', [Attr.noEsc]: '' },
		App.root
	);

	modal.innerHTML = html`
		<div class="${CSS.modalDialog}">
			<div class="${CSS.modalBody}">${text}</div>
			<div class="${CSS.modalFooter}">
				<am-modal-close class="${CSS.button}">
					${App.text('cancel')}
				</am-modal-close>
				<am-modal-close
					${Attr.confirm}
					class="${CSS.button} ${CSS.buttonAccent}"
				>
					${App.text('ok')}
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
					element.matches(`[${Attr.confirm}]`) ||
					element.closest(`[${Attr.confirm}]`) != null;
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
 * Resolve a file URL.
 *
 * @param fileUrl
 * @returns the resolved URL
 */
export const resolveFileUrl = (fileUrl: string): string => {
	if (!fileUrl) {
		return '';
	}

	if (fileUrl.match(/^\//)) {
		return `${App.baseURL}${fileUrl}`;
	}

	if (fileUrl.match(/:\/\//g)) {
		return fileUrl;
	}

	const pageUrl = getPageURL();

	if (pageUrl) {
		const page = App.pages[pageUrl];

		if (page) {
			return `${App.baseURL}/pages${page.path}${fileUrl}`;
		}
	}

	return '';
};

/**
 * Set the document title.
 *
 * @param page
 */
export const setDocumentTitle = (page: string): void => {
	document.title = `${page} — Automad`;
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
