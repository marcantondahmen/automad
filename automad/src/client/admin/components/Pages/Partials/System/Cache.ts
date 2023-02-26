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
 * Copyright (c) 2022-2023 by Marc Anton Dahmen
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
import { SelectComponent } from '../../../Select';
import { SystemComponent } from '../../System';

/**
 * Create bindings for the form elements in the cache section.
 *
 * @param listeners
 */
const createBindings = (component: SystemComponent): void => {
	const cacheEnabled = new Binding('cacheEnabled', {
		initial: App.system.cache.enabled,
	});

	const cacheMonitorDelay = new Binding('cacheMonitorDelay', {
		initial: App.system.cache.monitorDelay,
	});

	const cacheLifetime = new Binding('cacheLifetime', {
		initial: App.system.cache.lifetime,
	});

	component.addListener(
		listen(window, EventName.appStateChange, () => {
			cacheEnabled.value = App.system.cache.enabled;
			cacheMonitorDelay.value = App.system.cache.monitorDelay;
			cacheLifetime.value = App.system.cache.lifetime;
		})
	);
};

/**
 * Render the cache section.
 *
 * @param component
 * @returns the rendered HTML
 */
export const renderCacheSection = (component: SystemComponent): string => {
	createBindings(component);

	return html`
		<div class="${CSS.flex} ${CSS.flexColumn} ${CSS.flexGapLarge}">
			<am-form
				class="${CSS.flex} ${CSS.flexColumn} ${CSS.flexGapLarge}"
				${Attr.api}="Config/update"
				${Attr.event}="${EventName.appStateRequireUpdate}"
				${Attr.auto}
			>
				<input type="hidden" name="type" value="cache" />
				<div>
					<p>${App.text('systemCacheInfo')}</p>
					${createField(
						'am-toggle-large',
						null,
						{
							key: 'cacheEnabled',
							value: App.system.cache.enabled,
							name: 'cacheEnabled',
							label: App.text('systemCacheEnable'),
						},
						[],
						{
							[Attr.toggle]: '.am-cache-settings',
							[Attr.bind]: 'cacheEnabled',
							[Attr.bindTo]: 'checked',
						}
					).outerHTML}
				</div>
				<div class="am-cache-settings">
					<p>${App.text('systemCacheMonitorInfo')}</p>
					${SelectComponent.create(
						[
							{ value: 60, text: '1 min' },
							{ value: 120, text: '2 min' },
							{ value: 300, text: '5 min' },
							{ value: 600, text: '10 min' },
						],
						'',
						null,
						'cacheMonitorDelay',
						'',
						App.text('systemCacheMonitor'),
						[CSS.selectInline],
						{
							[Attr.bind]: 'cacheMonitorDelay',
							[Attr.bindTo]: 'value',
						}
					).outerHTML}
					<p>${App.text('systemCacheLifetimeInfo')}</p>
					${SelectComponent.create(
						[
							{ value: 3600, text: '1 h' },
							{ value: 21600, text: '6 h' },
							{ value: 43200, text: '12 h' },
							{ value: 86400, text: '24 h' },
						],
						'',
						null,
						'cacheLifetime',
						'',
						App.text('systemCacheLifetime'),
						[CSS.selectInline],
						{
							[Attr.bind]: 'cacheLifetime',
							[Attr.bindTo]: 'value',
						}
					).outerHTML}
				</div>
			</am-form>
			<am-form class="am-cache-settings" ${Attr.api}="Cache/clear">
				<p>${App.text('systemCacheClearInfo')}</p>
				<am-submit class="${CSS.button} ${CSS.buttonAccent}">
					${App.text('systemCacheClear')}
				</am-submit>
			</am-form>
			<am-form class="am-cache-settings" ${Attr.api}="Cache/purge">
				<p>${App.text('systemCachePurgeInfo')}</p>
				<am-submit class="${CSS.button}">
					${App.text('systemCachePurge')}
					<span class="${CSS.badge} ${CSS.badgeMuted}">
						${App.system.tempDirectory}
					</span>
				</am-submit>
			</am-form>
		</div>
	`;
};
