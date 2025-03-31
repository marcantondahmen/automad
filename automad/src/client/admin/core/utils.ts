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

import { App, Attr, CSS, create, html, listen, query } from '.';
import { Listener } from '@/admin/types';
import { customAlphabet } from 'nanoid';

/**
 * Return the basename of a path.
 *
 * @param path
 * @return the basename
 */
export const basename = (path: string): string => {
	return path.split('/').reverse()[0];
};

/**
 * Convert a block template file path into a human readable string.
 *
 * @param path
 * @return the human readable template name
 */
export const blockTemplateName = (path: string): string => {
	return `${titleCase(basename(path).replace('.php', ''))} &mdash; ${dirname(dirname(dirname(path))).replace(/^\//, '')}`;
};

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
		getComponentTargetContainer()
	);

	modal.innerHTML = html`
		<am-modal-dialog>
			<am-modal-body>${text}</am-modal-body>
			<am-modal-footer>
				<am-modal-close class="${CSS.button}">
					${App.text('cancel')}
				</am-modal-close>
				<am-modal-close
					${Attr.confirm}
					class="${CSS.button} ${CSS.buttonPrimary}"
				>
					${App.text('ok')}
				</am-modal-close>
			</am-modal-footer>
		</am-modal-dialog>
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
 * Convert RGB color to HEX.
 *
 * @param color
 * @return the HEX value
 */
export const convertRgbToHex = (color: string): string => {
	const rgb = color.match(/(\d+)/g);

	let hexr = parseInt(rgb[0]).toString(16);
	let hexg = parseInt(rgb[1]).toString(16);
	let hexb = parseInt(rgb[2]).toString(16);

	hexr = hexr.length === 1 ? '0' + hexr : hexr;
	hexg = hexg.length === 1 ? '0' + hexg : hexg;
	hexb = hexb.length === 1 ? '0' + hexb : hexb;

	return '#' + hexr + hexg + hexb;
};

/**
 * Return the basename of a path.
 *
 * @param path
 * @return the basename
 */
export const dirname = (path: string): string => {
	return path.replace(/\/[^\/]+$/, '');
};

/**
 * Get the container element where components can be created savely in the dashboard
 * as well as in the in-page edit mode.
 *
 * @return the container element
 */
export const getComponentTargetContainer = () => {
	return query('body .am-ui, .am-ui body');
};

/**
 * Get the meta key symbol based on the current client OS.
 *
 * @return For mac return "⌘", else "Ctrl"
 */
export const getMetaKeyLabel = (): string => {
	if (navigator.userAgent.toLowerCase().indexOf('mac') != -1) {
		return '⌘';
	}

	return 'Ctrl';
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
		.replace(/_/g, ' ')
		.split(' ')
		.map((word) => word.charAt(0).toUpperCase() + word.slice(1))
		.join(' ');
};

/**
 * Create a unique id with that can be used for the "id" attribute (starting with a letter).
 *
 * @see {@link docs https://github.com/ai/nanoid/#readme}
 * @returns the unique id
 */
export const uniqueId = () => {
	const prefix = customAlphabet(
		'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
		2
	);

	const main = customAlphabet(
		'0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
		30
	);

	return `${prefix()}${main()}`;
};
