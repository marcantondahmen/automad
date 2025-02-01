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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	APIResponse,
	controllerRoute,
	InPageController,
	RequestKey,
} from '@/common';

/**
 * Make a request to the API from an InPage component.
 *
 * @param api
 * @param controller
 * @param csrf
 * @param data
 * @return the response data object
 */
export const inPageRequest = async (
	api: string,
	controller: InPageController,
	csrf: string,
	data: { [key: string]: string | boolean }
): Promise<APIResponse> => {
	log(`${controller} ${'>>'}`, data);

	const formData = new FormData();

	formData.append(RequestKey.csrf, csrf);
	formData.append(RequestKey.json, JSON.stringify(data));

	const init: RequestInit = {
		method: 'POST',
		body: formData,
	};

	const response = await fetch(`${api}/${controllerRoute(controller)}`, init);
	const responseData = await response.json();

	log(`${controller} ${'<<'}`, responseData);

	return responseData;
};

/**
 * Log only in development mode.
 */
const log = (...items: any): void => {
	if (DEVELOPMENT) {
		console.log(...items);
	}
};
