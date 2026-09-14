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

import { KeyValueMap } from './types';

/**
 * The names of field that are submitted along with post requests.
 */
export const enum RequestKey {
	csrf = '__csrf__',
}

/**
 * Convert a controller name into a valid route.
 *
 * @param controller
 * @return the route
 */
export const controllerRoute = (controller: string): string => {
	const [controllerClass, method] = controller.split('::');
	const convert = (part: string) => {
		return part
			.replace(/([A-Z])/g, ' $1')
			.trim()
			.toLowerCase()
			.replace(/\s/g, '-');
	};

	return `${convert(controllerClass.replace('Controller', ''))}/${convert(
		method
	)}`;
};

/**
 * A common request wrapper that send json formatted data.
 *
 * @param url
 * @param data
 * @param csrf
 * @async
 */
export const post = async (
	url: string,
	data: KeyValueMap,
	csrf: string,
	signal: AbortSignal = null
): Promise<Response> => {
	return await fetch(url, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			...data,
			[RequestKey.csrf]: csrf,
		}),
		signal,
	});
};
