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

import { App, create, fire, notifyError, query } from '.';
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
 * Optionally execute a callback function that takes the response data as argument.
 * This is useful, in case the execution of the callback is critical and
 * has to be within the timespan of a pending request.
 *
 * @see {@link request}
 * @param route
 * @param [data]
 * @param [callback]
 * @returns the Promise
 * @async
 */
export const requestAPI = async (
	route: string,
	data: KeyValueMap = null,
	parallel: boolean = true,
	callback: Function = null
): Promise<KeyValueMap> => {
	if (parallel === false) {
		await waitForPendingRequests();
	}

	PendingRequests.add();

	let responseData;

	try {
		const response = await request(`${App.baseURL}/api/${route}`, data);
		responseData = await response.json();

		if (typeof callback === 'function') {
			callback.apply(this, [responseData]);
		}
	} catch {
		notifyError(`${App.text('fetchingDataError')} (${route})`);
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
	 *
	 * @static
	 */
	static eventName = 'AutomadPendingRequestsChange';

	/**
	 * The number of currently pending requests.
	 *
	 * @static
	 */
	private static count: number = 0;

	/**
	 * Return true if the number of pending request is 0.
	 *
	 * @static
	 */
	static get idle(): boolean {
		return this.count <= 0;
	}

	/**
	 * Add a request.
	 *
	 * @static
	 */
	static add() {
		if (!query('am-spinner')) {
			create('am-spinner', [], {}, document.body);
		}

		this.count++;
		fire(this.eventName);
	}

	/**
	 * Remove a request.
	 *
	 * @static
	 */
	static remove() {
		const spinner = query('am-spinner');

		this.count--;

		if (this.idle && spinner) {
			spinner.remove();
		}

		fire(this.eventName);
	}
}
