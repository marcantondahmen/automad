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

import { App } from './app';
import { KeyValueMap } from './types';

/**
 * Request a given URL and optionally post an object as data. When no data is passed, the request mehod will automatically be `GET`.
 *
 * @param url
 * @param [data]
 * @returns the Promise
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
	return request(`${App.dashboardURL}${slug}`, data);
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
