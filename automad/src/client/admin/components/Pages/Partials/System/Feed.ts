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
	createField,
	eventNames,
	html,
	listen,
} from '../../../../core';
import { Listener } from '../../../../types';

/**
 * Create bindings for the form elements in the feed section.
 *
 * @param listeners
 */
const createBindings = (listeners: Listener[]): void => {
	const feedEnabled = new Binding(
		'feedEnabled',
		null,
		null,
		App.system.feed.enabled
	);

	const feedFields = new Binding(
		'feedFields',
		null,
		null,
		JSON.stringify(App.system.feed.fields)
	);

	listeners.push(
		listen(window, eventNames.appStateChange, () => {
			feedEnabled.value = App.system.feed.enabled;
			feedFields.value = JSON.stringify(App.system.feed.fields);
		})
	);
};

/**
 * Render the feed section.
 *
 * @param listeners
 * @returns the rendered HTML
 */
export const renderFeedSection = (listeners: Listener[]): string => {
	createBindings(listeners);

	return html`
		<am-form
			api="Config/update"
			event="${eventNames.appStateRequireUpdate}"
			auto
		>
			<input type="hidden" name="type" value="feed" />
			<p>$${App.text('systemRssFeedInfo')}</p>
			${createField(
				'am-checkbox-large',
				null,
				{
					key: 'feedEnabled',
					value: App.system.feed.enabled,
					name: 'feedEnabled',
					label: App.text('systemRssFeedEnable'),
				},
				[],
				{
					bind: 'feedEnabled',
					bindto: 'checked',
					toggle: '#am-feed-settings',
				}
			).outerHTML}
			<div id="am-feed-settings">
				<p>$${App.text('systemRssFeedUrl')}</p>
				<div class="${classes.flex}">
					<input
						class="${classes.input}"
						value="${App.feedURL}"
						disabled
					/>
					<am-copy class="${classes.button}" value="${App.feedURL}">
						<i class="bi bi-clipboard"></i>
					</am-copy>
				</div>
				<p>$${App.text('systemRssFeedFields')}</p>
				${createField(
					'am-feed-field-select',
					null,
					{
						key: 'feedFields',
						value: '',
						name: 'feedFields',
					},
					[],
					{
						bind: 'feedFields',
						bindto: 'value',
					}
				).outerHTML}
			</div>
		</am-form>
	`;
};
