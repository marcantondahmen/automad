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

import { KeyValueMap, Partials } from '../types';
import { App } from '.';

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
	 * {{ app:sections.content.files }}
	 * {{ app:base }}
	 */
	template = template.replace(
		/\{\{\s*(\w+):([\.\w]+)\s*\}\}/g,
		(match: string, $1: string, $2: string): string => {
			switch ($1) {
				case 'app':
					return resolve(App.state, $2);
				case 'text':
					return App.text($2);
			}

			return '';
		}
	);

	return template;
};

/**
 * Resolve a dot notation path and return the corresponding value of a given nested object structure.
 *
 * @param object
 * @param $2
 * @returns the resolved value
 */
const resolve = (object: KeyValueMap, $2: string): any => {
	const parts = $2.split('.');
	let temp = object;

	parts.forEach((key) => {
		temp = temp[key];
	});

	return temp;
};
