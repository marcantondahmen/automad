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
 * Copyright (c) 2023-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { App } from './app';

/**
 * Format dates by the locale that is defined in the translations.
 *
 * @param timestamp
 * @return the formatted date string
 */
export const dateFormat = (timestamp: string): string => {
	const lang = App.text('__lang__');
	const date = new Date(timestamp);
	const now = new Date();
	const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
	const other = new Date(date.getFullYear(), date.getMonth(), date.getDate());

	// Diff must be rounded in order to work with daylight saving time changes as well.
	const diffDays = Math.round((other.getTime() - today.getTime()) / 86400000);

	const day =
		diffDays === 0
			? App.text('today')
			: diffDays === -1
				? App.text('yesterday')
				: date.toLocaleDateString(lang, {
						year: 'numeric',
						month: 'short',
						weekday: 'short',
						day: '2-digit',
					});

	const time = date.toLocaleTimeString(lang, {
		hour: '2-digit',
		minute: '2-digit',
		hour12: false,
	});

	return `${day}, ${time}`;
};
