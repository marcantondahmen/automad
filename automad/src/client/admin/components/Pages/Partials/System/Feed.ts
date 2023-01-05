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
	CSS,
	EventName,
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
	const feedEnabled = new Binding('feedEnabled', {
		initial: App.system.feed.enabled,
	});

	const feedFields = new Binding('feedFields', {
		initial: JSON.stringify(App.system.feed.fields),
	});

	listeners.push(
		listen(window, EventName.appStateChange, () => {
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
			class="${CSS.flex} ${CSS.flexColumn} ${CSS.flexGapLarge}"
			${Attr.api}="Config/update"
			${Attr.event}="${EventName.appStateRequireUpdate}"
			${Attr.auto}
		>
			<input type="hidden" name="type" value="feed" />
			<div>
				<p>${App.text('systemRssFeedInfo')}</p>
				${createField(
					'am-toggle-large',
					null,
					{
						key: 'feedEnabled',
						value: App.system.feed.enabled,
						name: 'feedEnabled',
						label: App.text('systemRssFeedEnable'),
					},
					[],
					{
						[Attr.bind]: 'feedEnabled',
						[Attr.bindTo]: 'checked',
						[Attr.toggle]: '#am-feed-settings',
					}
				).outerHTML}
			</div>
			<div id="am-feed-settings">
				<p>${App.text('systemRssFeedUrl')}</p>
				<div class="${CSS.formGroup}">
					<input
						class="${CSS.input} ${CSS.flexItemGrow} ${CSS.formGroupItem}"
						value="${App.feedURL}"
						disabled
					/>
					<am-copy
						class="${CSS.button} ${CSS.buttonIcon} ${CSS.formGroupItem}"
						value="${App.feedURL}"
						${Attr.tooltip}="${App.text('copyUrlClipboard')}"
					>
						<i class="bi bi-clipboard"></i>
					</am-copy>
				</div>
				<p>${App.text('systemRssFeedFields')}</p>
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
						[Attr.bind]: 'feedFields',
						[Attr.bindTo]: 'value',
					}
				).outerHTML}
			</div>
		</am-form>
	`;
};
