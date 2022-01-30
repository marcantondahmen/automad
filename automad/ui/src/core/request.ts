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

import { App, fire, notifyError } from '.';
import { KeyValueMap } from '../types';

/**
 * Request a given URL and optionally post an object as data.
 * When no data is passed, the request mehod will automatically be `GET`.
 *
 * @param url
 * @param [data]
 * @returns the Promise
 * @async
 */
export const request = async (
	url: string,
	data: KeyValueMap = null
): Promise<Response> => {
	const init: KeyValueMap = { method: 'GET' };

	if (data !== null) {
		const formData = new FormData();

		Object.keys(data).forEach((key) => {
			formData.append(key, data[key]);
		});

		init.method = 'POST';
		init.body = formData;
		init.headers = {};
	}

	return fetch(url, init);
};

/**
 * Send a request to an API endpoint such as `Page/data`.
 *
 * @see {@link request}
 * @param route
 * @param [data]
 * @returns the Promise
 * @async
 */
export const requestAPI = async (
	route: string,
	data: KeyValueMap = null
): Promise<KeyValueMap> => {
	PendingRequests.add();

	let responseData;

	try {
		const response = await request(`${App.baseURL}/api/${route}`, data);
		responseData = await response.json();
	} catch {
		notifyError(`${App.text('error_request')} (${route})`);
		responseData = {};
	}

	PendingRequests.remove();

	return responseData;
};

/**
 * Wait for pending requests to be finished.
 *
 * @returns a promise that resolves as soon there is no pending request
 */
export const waitForPendingRequests = async (): Promise<any> => {
	return new Promise((resolve, reject) => {
		const checkPendingRequests = () => {
			if (PendingRequests.idle) {
				resolve(true);

				window.removeEventListener(
					PendingRequests.eventName,
					checkPendingRequests
				);
			}
		};

		window.addEventListener(
			PendingRequests.eventName,
			checkPendingRequests
		);

		checkPendingRequests();
	});
};

/**
 * A util class that tracks the number of pending requests.
 */
class PendingRequests {
	/**
	 * The event name that is used when the count changes.
	 */
	static eventName = 'AutomadPendingRequestsChange';

	/**
	 * The number of currently pending requests.
	 */
	private static count: number = 0;

	/**
	 * Return true if the number of pending request is 0.
	 */
	static get idle(): boolean {
		return this.count <= 0;
	}

	/**
	 * Add a request,
	 */
	static add() {
		this.count++;
		fire(this.eventName);
	}

	/**
	 * Remove a request.
	 */
	static remove() {
		this.count--;
		fire(this.eventName);
	}
}
