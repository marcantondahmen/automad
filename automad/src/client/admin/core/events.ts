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

import { Listener } from '@/admin/types';
import { CSS, getLogger, queryAll } from '.';

/**
 * The object with all custom event that are used by the UI.
 */
export const enum EventName {
	appStateChange = 'AutomadAppStateChange',
	appStateRequireUpdate = 'AutomadAppStateRequireUpdate',
	autocompleteSelect = 'AutomadAutocompleteSelect',
	beforeUpdateView = 'AutomadBeforeUpdateView',
	changeByBinding = 'AutomadChangeByBinding',
	contentPublished = 'AutomadContentPublished',
	contentSaved = 'AutomadContentSaved',
	dashboardThemeChange = 'AutomadDashboardThemeChange',
	fileCollectionRender = 'AutomadFileCollectionRender',
	filesChangeOnServer = 'AutomadFilesChangeOnServer',
	modalClose = 'AutomadModalClose',
	modalOpen = 'AutomadModalOpen',
	repositoriesChange = 'AutomadRepositoriesChange',
	packagesChange = 'AutomadPackagesChange',
	packagesUpdateCheck = 'AutomadPackagesUpdateCheck',
	portalChange = 'AutomadPortalChange',
	switcherChange = 'AutomadSwitcherChange',
	systemUpdateCheck = 'AutomadSystemUpdateCheck',
	undoStackUpdate = 'AutomadUndoStackUpdate',
}

/**
 * Create a listener that traps focus inside of a given container.
 *
 * @param container
 * @return the created listener
 */
export const createFocusTrap = (container: HTMLElement): Listener => {
	const getElements = () => {
		return queryAll(
			'input:not([type="hidden"]), button, textarea, select, a[href], [contenteditable], [tabindex="0"]',
			container
		);
	};

	let current: HTMLElement = undefined;

	return listen(document, 'keydown', (event: KeyboardEvent) => {
		if (!(event.key === 'Tab')) {
			return;
		}

		event.preventDefault();
		event.stopImmediatePropagation();
		event.stopPropagation();

		const elements = getElements();
		const lastIndex = elements.length - 1;
		const first = elements[0];
		const last = elements[lastIndex];

		if (!first || !last) {
			return;
		}

		const currentIndex = elements.indexOf(current ?? first);
		const nextIndex = currentIndex >= lastIndex ? 0 : currentIndex + 1;
		const prevIndex = currentIndex <= 0 ? lastIndex : currentIndex - 1;
		const newIndex = event.shiftKey ? prevIndex : nextIndex;

		current = elements[newIndex];
		current.focus();
	});
};

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
	callback: (event: Event) => void,
	selector: string = ''
): Listener => {
	const eventNames = eventNamesString
		.split(' ')
		.filter((str) => str.length > 0);

	let handler = (event: Event) => {
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

		handler = null;
	};

	return { remove };
};

/**
 * Listen to class changes by using a MutationObserver.
 *
 * @param element
 * @param callback
 * @param subtree
 * @returns a special listener that listens for class changes
 */
export const listenToClassChange = (
	element: HTMLElement,
	callback: (mutation: MutationRecord) => void,
	subtree: boolean = true
): Listener => {
	const observer = new MutationObserver((mutationList, observer) => {
		mutationList.forEach(function (mutation) {
			if (
				mutation.type === 'attributes' &&
				mutation.attributeName === 'class'
			) {
				callback.apply(mutation.target, [mutation]);
			}
		});
	});

	observer.observe(element, {
		attributes: true,
		childList: false,
		subtree,
	});

	const remove = () => {
		observer.disconnect();
	};

	return { remove };
};

/**
 * Make any focused element clickable using the enter key.
 *
 * @return the listener
 */
export const initEnterKeyHandler = (): Listener => {
	return listen(document, 'keydown', (event: KeyboardEvent): void => {
		if (event.keyCode == 13) {
			(document.activeElement as HTMLButtonElement).click();
		}
	});
};

/**
 * Add validate class to changed input field.
 *
 * @returns the listener
 */
export const initInputChangeHandler = (): Listener => {
	return listen(
		document,
		'change',
		(event: Event) => {
			const input = event.target as HTMLElement;

			input.classList.add(CSS.validate);
		},
		'input, textarea, [contenteditable]'
	);
};

/**
 * Initialize an error event handler that mutes a given set of errors.
 *
 * @return the listener
 */
export const initWindowErrorHandler = (): Listener => {
	const muted = ['InvalidStateError', 'TypeError'];

	return listen(window, 'error', (event: ErrorEvent) => {
		if (muted.includes(event.error.name)) {
			getLogger().log(event.error);
			event.preventDefault();
		}
	});
};
