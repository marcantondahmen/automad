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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

/**
 * This is a minimal theme getter that is loaded separately from the main index file
 * in order to set the correct color scheme for the dashboard as soon as possible and
 * show the preloading animation with the correct colors to avoid flashing screen.
 */

export enum DashboardTheme {
	light = 'light',
	lowContrast = 'low-contrast',
	dark = 'dark',
}

export const DASHBOARD_THEME_KEY = 'am-dashboard-theme';

/**
 * Get the color scheme from local storage or system preferences.
 *
 * @returns The current color scheme in use
 */
export const getTheme = (): DashboardTheme => {
	const localScheme = localStorage.getItem(DASHBOARD_THEME_KEY);

	if (localScheme) {
		return localScheme as DashboardTheme;
	}

	if (
		window.matchMedia &&
		window.matchMedia('(prefers-color-scheme: dark)').matches
	) {
		return DashboardTheme.dark;
	}
};

/**
 * The theme is set asap when this module is loaded
 * before the actual admin index module is ready.
 */
document.documentElement.classList.add(getTheme());
