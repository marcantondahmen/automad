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

import { Listener } from '../types';
import { query, queryAll } from './utils';

/**
 * The object with all custom event that are used by the UI.
 */
export enum EventName {
	appStateChange = 'AutomadAppStateChange',
	appStateRequireUpdate = 'AutomadAppStateRequireUpdate',
	autocompleteSelect = 'AutomadAutocompleteSelect',
	changeByBinding = 'AutomadChangeByBinding',
	fileCollectionRender = 'AutomadFileCollectionRender',
	filesChangeOnServer = 'AutomadFilesChangeOnServer',
	modalClose = 'AutomadModalClose',
	modalOpen = 'AutomadModalOpen',
	packagesChange = 'AutomadPackagesChange',
	switcherChange = 'AutomadSwitcherChange',
	systemUpdateCheck = 'AutomadSystemUpdateCheck',
}

/**
 * Fires an event on an element or the window.
 *
 * @param name
 * @param [element]
 */
export const fire = (
	name: string,
	element: HTMLElement | Document | Window = window
): void => {
	element.dispatchEvent(new Event(name, { bubbles: true }));
};

/**
 * Register event listeners.
 *
 * @param element - the element to register the event listeners to
 * @param eventNamesString - a string of one or more event names separated by a space
 * @param callback - the callback
 * @param [selector] - the sector to be used as filter
 * @return listener object
 */
export const listen = (
	element: HTMLElement | Document | Window,
	eventNamesString: string,
	callback: Function,
	selector: string = ''
): Listener => {
	const eventNames = eventNamesString
		.split(' ')
		.filter((str) => str.length > 0);

	const handler = (event: Event) => {
		if (!selector) {
			callback.apply(event.target, [event]);
			return;
		}

		const path = event.path || (event.composedPath && event.composedPath());

		path.forEach((_element: any) => {
			try {
				if (_element.matches(selector)) {
					callback.apply(event.target, [event]);
					return;
				}
			} catch (error) {}
		});
	};

	eventNames.forEach((eventName) => {
		element.addEventListener(eventName, handler);
	});

	const remove = () => {
		eventNames.forEach((eventName) => {
			element.removeEventListener(eventName, handler);
		});
	};

	return { remove };
};

/**
 * Create a listener that traps focus inside of a given container.
 *
 * @param container
 * @return the created listener
 */
export const createFocusTrap = (container: HTMLElement): Listener => {
	const selector =
		'input:not([type="hidden"]), button, textarea, [contenteditable]';

	query(selector, container)?.focus();

	return listen(document, 'keydown', (event: KeyboardEvent): void => {
		if (!(event.key === 'Tab' || event.keyCode === 9)) {
			return;
		}
		const elements = queryAll(selector, container);

		if (elements.length === 0) {
			event.preventDefault();

			return;
		}

		const first = elements[0];
		const last = elements[elements.length - 1];

		if (event.shiftKey) {
			if (document.activeElement === first) {
				event.preventDefault();
				last.focus();
			}

			return;
		}

		if (document.activeElement === last) {
			event.preventDefault();
			first.focus();
		}
	});
};
