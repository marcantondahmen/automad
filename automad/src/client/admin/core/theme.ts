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

import { EventName, fire, query } from '.';

export enum DashboardTheme {
	light = 'light',
	lowContrast = 'low-contrast',
	dark = 'dark',
}

const DASHBOARD_THEME_KEY = 'am-dashboard-theme';

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
 * Set the color scheme and apply it to the current page.
 *
 * @param theme
 */
export const setTheme = (theme: DashboardTheme): void => {
	localStorage.setItem(DASHBOARD_THEME_KEY, theme);
	applyTheme(theme);

	fire(EventName.dashboardThemeChange);
};

/**
 * Apply a color scheme'
 *
 * @param theme
 */
export const applyTheme = (theme: DashboardTheme): void => {
	const ui = query('.am-ui');

	Object.values(DashboardTheme).forEach((item) => {
		ui.classList.toggle(item, theme === item);
	});
};
