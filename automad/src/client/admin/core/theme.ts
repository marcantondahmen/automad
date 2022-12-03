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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { query } from './utils';

export enum DashboardThemes {
	light = 'light',
	lowContrast = 'low-contrast',
	dark = 'dark',
}

/**
 * Get the color scheme from local storage or system preferences.
 *
 * @returns The current color scheme in use
 */
export const getTheme = (): DashboardThemes => {
	const localScheme = localStorage.getItem('dashboard-theme');

	if (localScheme) {
		return localScheme as DashboardThemes;
	}

	if (
		window.matchMedia &&
		window.matchMedia('(prefers-color-scheme: dark)').matches
	) {
		return DashboardThemes.dark;
	}
};

/**
 * Set the color scheme and apply it to the current page.
 *
 * @param theme
 */
export const setTheme = (theme: DashboardThemes): void => {
	localStorage.setItem('dashboard-theme', theme);
	applyTheme(theme);
};

/**
 * Apply a color scheme'
 *
 * @param theme
 */
export const applyTheme = (theme: DashboardThemes): void => {
	const ui = query('.am-ui');

	Object.values(DashboardThemes).forEach((item) => {
		ui.classList.toggle(item, theme === item);
	});
};
