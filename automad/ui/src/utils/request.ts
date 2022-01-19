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

import { getDashboardURL } from './core';
import { KeyValueMap } from './types';
import md5 from 'crypto-js/md5';

/**
 * Request a given URL and optionally post an object as data. When no data is passed, the request mehod will automatically be `GET`.
 *
 * @param url
 * @param [data]
 * @returns the Promise
 */
const request = async (
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
 * Use the `request` function to send a request to a dashboard URL.
 *
 * @see {@link request}
 * @param slug
 * @param [data]
 * @returns the Promise
 */
const requestDashboard = async (
	slug: string,
	data: KeyValueMap = null
): Promise<Response> => {
	const dashboard = getDashboardURL();

	if (dashboard) {
		return request(`${dashboard}${slug}`, data);
	}
};

/**
 * Use the `requestDashboard` function to send a request to a controller such as `PageController::data`.
 * The controller will automatically converted to the `Page/data` route.
 *
 * @see {@link request}
 * @see {@link requestDashboard}
 * @param controller
 * @param [data]
 * @returns the Promise
 */
export const requestController = async (
	controller: string,
	data: KeyValueMap = null
): Promise<KeyValueMap> => {
	const route = controller.replace('Controller::', '/');
	const response = await requestDashboard(`/api/${route}`, data);
	const responseData = await response.json();

	return responseData;
};

interface ControllerRequestHashmap {
	[key: string]: Promise<KeyValueMap>;
}

/**
 * A wrapper class to cache requests to the same controller using the same data
 * and re-use its responses instead of requesting the backend multiple times.
 */
export class CachedControllerRequest {
	/**
	 * The actual hashmap to store the returned promises.
	 */
	static cache: ControllerRequestHashmap = {};

	/**
	 * Fetch a controller in case there is no cached promise yet.
	 *
	 * @param controller
	 * @param data
	 * @returns the promise that is returned by the controller request or the cached one
	 */
	static async fetch(
		controller: string,
		data: KeyValueMap = null
	): Promise<KeyValueMap> {
		const hash = md5(JSON.stringify({ controller, data })).toString();

		if (typeof this.cache[hash] === 'undefined') {
			this.cache[hash] = requestController(controller, data);
		}

		return this.cache[hash];
	}
}
