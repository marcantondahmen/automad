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

import { App } from '.';

export enum Routes {
	login,
	home,
	page,
}

/**
 * Get the page slug from a dashboard URL.
 *
 * @returns the slug
 */
const getSlug = (): string => {
	const regex = new RegExp(`^${App.dashboardURL}\/`, 'i');
	return window.location.pathname.replace(regex, '');
};

/**
 * Test whether a route is in the routes array.
 *
 * @param route
 * @returns true if the route is a defined route
 */
const isValidRoute = (route: string) => {
	return route && route in Routes;
};

/**
 * Get the slug from the page URL or redirect to the home page in case the route is invalid.
 *
 * @returns a valid route or redirect in case the slug is unknown
 */
export const getRouteOrRedirect = (): string => {
	const slug = getSlug();

	if (isValidRoute(slug)) {
		return slug;
	}

	window.location.href = `${App.dashboardURL}/${Routes[Routes.home]}`;
};

/**
 * Convert a route into a tag name.
 *
 * @param route
 * @returns the tag namm
 */
export const getTagFromRoute = (route: string): string => {
	return `am-${route}`;
};
