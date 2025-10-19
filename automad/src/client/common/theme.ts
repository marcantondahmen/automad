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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
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
