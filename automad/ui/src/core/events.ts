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

import { KeyValueMap, Listener } from '../types';

/**
 * The object with all custom event that are used by the UI.
 */
export const eventNames: KeyValueMap = {
	appStateChange: 'AutomadAppStateChange',
	fileCollectionRender: 'AutomadFileCollectionRender',
	filesChangeOnServer: 'AutomadFilesChangeOnServer',
	modalClose: 'AutomadModalClose',
	modalOpen: 'AutomadModalOpen',
	switcherChange: 'AutomadSwitcherChange',
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
