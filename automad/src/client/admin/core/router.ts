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
 * Copyright (c) 2021-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { App, routes } from '.';

/**
 * Get the page slug from a dashboard URL.
 *
 * @returns the slug
 */
export const getSlug = (): string => {
	const regex = new RegExp(`^${App.dashboardURL}\/`, 'i');

	return window.location.pathname.replace(regex, '');
};

/**
 * Test whether a route is in the routes object.
 *
 * @param route
 * @returns true if the route is a defined route
 */
const isValidRoute = (route: string) => {
	return (
		!!route &&
		Object.values(routes).includes(
			route as (typeof routes)[keyof typeof routes]
		)
	);
};

/**
 * Get the slug from the page URL or redirect to
 * the home/login page in case the rout is invalid.
 *
 * @returns a valid route the home page in case the slug is unknown
 */
export const getValidRouteOrRedirect = (): string => {
	const slug = getSlug();

	if (isValidRoute(slug)) {
		return slug;
	}

	window.location.href = `${App.dashboardURL}/${routes.home}`;
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
