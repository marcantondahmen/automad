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

const requestDashboard = async (slug, data = null) => {
	const dashboard = getDashboardURL();

	if (!dashboard) {
		return false;
	}

	return request(`${dashboard}${slug}`, data);
};

export const requestController = async (controller, data) => {
	const response = await requestDashboard(`?controller=${controller}`, data);
	const responseData = await response.json();

	return responseData;
};
