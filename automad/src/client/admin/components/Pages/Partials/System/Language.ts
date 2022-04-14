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

import {
	App,
	Binding,
	classes,
	eventNames,
	html,
	listen,
	renderOptions,
} from '../../../../core';
import { Listener } from '../../../../types';

/**
 * Create bindings for the form elements in the cache section.
 *
 * @param listeners
 */
const createBindings = (listeners: Listener[]): void => {
	const translation = new Binding(
		'translation',
		null,
		null,
		App.system.translation
	);

	listeners.push(
		listen(window, eventNames.appStateChange, () => {
			translation.value = App.system.translation;
		})
	);
};

/**
 * Render the cache section.
 *
 * @param listeners
 * @returns the rendered HTML
 */
export const renderLanguageSection = (listeners: Listener[]): string => {
	const languages = [];

	for (const [key, value] of Object.entries(App.languages)) {
		languages.push({ text: key, value });
	}

	createBindings(listeners);

	return html`
		<am-form
			api="Config/update"
			event="${eventNames.appStateRequireUpdate}"
			auto
		>
			<input type="hidden" name="type" value="translation" />
			<p>$${App.text('systemLanguageInfo')}</p>
			<p>
				<am-select class="${classes.button}">
					<span></span>
					<select
						name="translation"
						bind="translation"
						bindto="value"
					>
						${renderOptions(languages)}
					</select>
				</am-select>
			</p>
		</am-form>
	`;
};
