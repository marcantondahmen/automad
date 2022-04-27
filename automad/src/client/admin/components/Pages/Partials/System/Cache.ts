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
	renderOptions,
} from '../../../../core';
import { Listener } from '../../../../types';

/**
 * Create bindings for the form elements in the cache section.
 *
 * @param listeners
 */
const createBindings = (listeners: Listener[]): void => {
	const cacheEnabled = new Binding(
		'cacheEnabled',
		null,
		null,
		App.system.cache.enabled
	);

	const cacheMonitorDelay = new Binding(
		'cacheMonitorDelay',
		null,
		null,
		App.system.cache.monitorDelay
	);

	const cacheLifetime = new Binding(
		'cacheLifetime',
		null,
		null,
		App.system.cache.lifetime
	);

	listeners.push(
		listen(window, eventNames.appStateChange, () => {
			cacheEnabled.value = App.system.cache.enabled;
			cacheMonitorDelay.value = App.system.cache.monitorDelay;
			cacheLifetime.value = App.system.cache.lifetime;
		})
	);
};

/**
 * Render the cache section.
 *
 * @param listeners
 * @returns the rendered HTML
 */
export const renderCacheSection = (listeners: Listener[]): string => {
	createBindings(listeners);

	return html`
		<am-form
			api="Config/update"
			event="${eventNames.appStateRequireUpdate}"
			auto
		>
			<input type="hidden" name="type" value="cache" />
			<p>$${App.text('systemCacheInfo')}</p>
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
					toggle: '.am-cache-settings',
					bind: 'cacheEnabled',
					bindto: 'checked',
				}
			).outerHTML}
			<div class="am-cache-settings">
				<p>$${App.text('systemCacheMonitorInfo')}</p>
				<p>
					<am-select class="${classes.button}">
						$${App.text('systemCacheMonitor')}
						<span></span>
						<select
							name="cacheMonitorDelay"
							bind="cacheMonitorDelay"
							bindto="value"
						>
							${renderOptions([
								{ value: 60, text: '1 min' },
								{ value: 120, text: '2 min' },
								{ value: 300, text: '5 min' },
								{ value: 600, text: '10 min' },
							])}
						</select>
					</am-select>
				</p>
				<p>$${App.text('systemCacheLifetimeInfo')}</p>
				<p>
					<am-select class="${classes.button}">
						$${App.text('systemCacheLifetime')}
						<span></span>
						<select
							name="cacheLifetime"
							bind="cacheLifetime"
							bindto="value"
						>
							${renderOptions([
								{ value: 3600, text: '1 h' },
								{ value: 21600, text: '6 h' },
								{ value: 43200, text: '12 h' },
								{ value: 86400, text: '24 h' },
							])}
						</select>
					</am-select>
				</p>
			</div>
		</am-form>
		<am-form class="am-cache-settings" api="Cache/clear">
			<p>$${App.text('systemCacheClearInfo')}</p>
			<am-submit class="${classes.button}">
				$${App.text('systemCacheClear')}
			</am-submit>
		</am-form>
		<am-form class="am-cache-settings" api="Cache/purge">
			<p>$${App.text('systemCachePurgeInfo')}</p>
			<am-submit class="${classes.button}">
				$${App.text('systemCachePurge')}
				<span class="${classes.badge}">
					${App.system.tempDirectory}
				</span>
			</am-submit>
		</am-form>
	`;
};