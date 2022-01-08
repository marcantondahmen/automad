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

/**
 * Request a given URL and optionally post an object as data. When no data is passed, the request mehod will automatically be `GET`.
 *
 * @param {string} url
 * @param {Object} [data]
 * @returns {Promise}
 */
const request = async (url, data = null) => {
	const init = { method: 'GET' };

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
 * @param {string} slug
 * @param {Object} [data]
 * @returns {Promise}
 */
const requestDashboard = async (slug, data = null) => {
	const dashboard = getDashboardURL();

	if (!dashboard) {
		return false;
	}

	return request(`${dashboard}${slug}`, data);
};

/**
 * Use the `requestDashboard` function to send a request to a controller such as `PageController::data`.
 * The controller will automatically converted to the `Page/data` route.
 *
 * @see {@link request}
 * @see {@link requestDashboard}
 * @param {string} controller
 * @param {Object} [data]
 * @returns {Promise}
 */
export const requestController = async (controller, data = null) => {
	const route = controller.replace('Controller::', '/');
	const response = await requestDashboard(`/api/${route}`, data);
	const responseData = await response.json();

	return responseData;
};
