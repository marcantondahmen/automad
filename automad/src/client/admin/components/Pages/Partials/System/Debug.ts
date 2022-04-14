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
	createField,
	eventNames,
	html,
	listen,
} from '../../../../core';
import { Listener } from '../../../../types';

/**
 * Create bindings for the form elements in the debug section.
 *
 * @param listeners
 */
const createBindings = (listeners: Listener[]): void => {
	const debugEnabled = new Binding(
		'debugEnabled',
		null,
		null,
		App.system.debug
	);

	listeners.push(
		listen(window, eventNames.appStateChange, () => {
			debugEnabled.value = App.system.debug;
		})
	);
};

/**
 * Render the debug section.
 *
 * @param listeners
 * @returns the rendered HTML
 */
export const renderDebugSection = (listeners: Listener[]): string => {
	createBindings(listeners);

	return html`
		<am-form
			api="Config/update"
			event="${eventNames.appStateRequireUpdate}"
			auto
		>
			<input type="hidden" name="type" value="debug" />
			<p>$${App.text('systemDebugInfo')}</p>
			${createField(
				'am-checkbox-large',
				null,
				{
					key: 'debugEnabled',
					value: App.system.debug,
					name: 'debugEnabled',
					label: App.text('systemDebugEnable'),
				},
				[],
				{
					bind: 'debugEnabled',
					bindto: 'checked',
				}
			).outerHTML}
		</am-form>
	`;
};
