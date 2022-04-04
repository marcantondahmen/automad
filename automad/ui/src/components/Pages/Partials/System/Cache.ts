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
import { KeyValueMap, Listener } from '../../../../types';

const renderOptions = (options: KeyValueMap[]): string => {
	let output = '';

	options.forEach((option) => {
		output += html`
			<option value="${option.value}">${option.text}</option>
		`;
	});

	return output;
};

const createBindings = (listeners: Listener[]) => {
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
			<p>
				${createField(
					'am-checkbox-large',
					null,
					{
						key: 'enabled',
						value: App.system.cache.enabled,
						name: 'cache[enabled]',
						label: App.text('systemCacheEnable'),
					},
					[],
					{
						toggle: '.am-cache-settings',
						bind: 'cacheEnabled',
						bindto: 'checked',
					}
				).outerHTML}
			</p>
			<div class="am-cache-settings">
				<p>$${App.text('systemCacheMonitorInfo')}</p>
				<p>
					<am-select class="${classes.button}">
						$${App.text('systemCacheMonitor')}
						<span></span>
						<select
							name="cache[monitor-delay]"
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
							name="cache[lifetime]"
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
