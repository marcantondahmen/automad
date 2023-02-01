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
	createField,
	EventName,
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
	const debugEnabled = new Binding('debugEnabled', {
		initial: App.system.debug,
	});

	listeners.push(
		listen(window, EventName.appStateChange, () => {
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
			${Attr.api}="Config/update"
			${Attr.event}="${EventName.appStateRequireUpdate}"
			${Attr.auto}
		>
			<input type="hidden" name="type" value="debug" />
			<div>
				<p>${App.text('systemDebugInfo')}</p>
				${createField(
					'am-toggle-large',
					null,
					{
						key: 'debugEnabled',
						value: App.system.debug,
						name: 'debugEnabled',
						label: App.text('systemDebugEnable'),
					},
					[],
					{
						[Attr.bind]: 'debugEnabled',
						[Attr.bindTo]: 'checked',
					}
				).outerHTML}
			</div>
		</am-form>
	`;
};
