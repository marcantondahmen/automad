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
 * Copyright (c) 2024-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { APIResponse, controllerRoute, InPageController, post } from '@/common';

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

	const response = await post(
		`${api}/${controllerRoute(controller)}`,
		data || {},
		csrf
	);

	const responseData = await response?.json();

	log(`${controller} ${'<<'}`, responseData);

	return responseData || {};
};

/**
 * Log only in development mode.
 */
const log = (...items: any): void => {
	if (DEVELOPMENT) {
		console.log(...items);
	}
};
