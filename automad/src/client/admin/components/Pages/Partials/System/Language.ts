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
	Attr,
	Binding,
	CSS,
	EventName,
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
	const translation = new Binding('translation', {
		initial: App.system.translation,
	});

	listeners.push(
		listen(window, EventName.appStateChange, () => {
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
			${Attr.api}="Config/update"
			${Attr.event}="${EventName.appStateRequireUpdate}"
			${Attr.auto}
		>
			<input type="hidden" name="type" value="translation" />
			<div>
				<p>${App.text('systemLanguageInfo')}</p>
				<am-select class="${CSS.selectInline}">
					<span></span>
					<select
						name="translation"
						${Attr.bind}="translation"
						${Attr.bindTo}="value"
					>
						${renderOptions(languages)}
					</select>
				</am-select>
			</div>
		</am-form>
	`;
};
