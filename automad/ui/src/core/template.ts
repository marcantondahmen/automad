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

import { Partials } from '../types';
import { App } from './app';

/**
 * Render a template.
 *
 * @param template
 * @param partials
 * @returns the rendered and merged HTML
 */
export const renderTemplate = (
	template: string,
	partials: Partials
): string => {
	/**
	 * {% partial %}
	 */
	template = template.replace(
		/\{\%\s*(\w+)\s*\%\}/g,
		(match: string, partial: string) => partials[partial].apply(this)
	);

	/**
	 * {{ text:btn_save }}
	 * {{ app:base }}
	 */
	template = template.replace(
		/\{\{\s*(\w+):(\w+)\s*\}\}/g,
		(match: string, $1: string, $2: string): string => {
			switch ($1) {
				case 'app':
					return App.state[$2];
				case 'text':
					return App.text($2);
			}

			return '';
		}
	);

	return template;
};
