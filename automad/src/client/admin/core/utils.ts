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

import { App, Attr, CSS, create, html, listen } from '.';
import { Listener } from '../types';
import { nanoid } from 'nanoid';

/**
 * A simple confirmation modal.
 *
 * @param text
 * @async
 */
export const confirm = async (text: string): Promise<boolean> => {
	let modal = create(
		'am-modal',
		[],
		{
			[Attr.destroy]: '',
			[Attr.noClick]: '',
			[Attr.noEsc]: '',
			style: 'z-index: 2000;',
		},
		App.root
	);

	modal.innerHTML = html`
		<div class="${CSS.modalDialog}">
			<div class="${CSS.modalBody}">${text}</div>
			<div class="${CSS.modalFooter}">
				<am-modal-close class="${CSS.button} ${CSS.buttonPrimary}">
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

/**
 * Create a unique id with an optional prefix.
 *
 * @see {@link docs https://github.com/ai/nanoid/#readme}
 * @param prefix
 * @returns the optionally prefixed id
 */
export const uniqueId = (prefix = 'am-') => {
	return `${prefix}${nanoid(10)}`;
};
