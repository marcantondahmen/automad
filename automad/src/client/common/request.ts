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

/**
 * The names of field that are submitted along with post requests.
 */
export const enum RequestKey {
	csrf = '__csrf__',
	json = '__json__',
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
