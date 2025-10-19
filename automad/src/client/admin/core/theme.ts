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

import { DashboardTheme, DASHBOARD_THEME_KEY, EventName, fire, query } from '.';

export * from '@/common/theme';

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
