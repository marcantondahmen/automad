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

import { App, classes, createField, html } from '../../../../core';
import { KeyValueMap } from '../../../../types';

const renderOptions = (
	options: KeyValueMap[],
	selected: string | number
): string => {
	let output = '';

	options.forEach((option) => {
		output += html`
			<option
				value="${option.value}"
				${option.value == selected ? 'selected' : ''}
			>
				${option.text}
			</option>
		`;
	});

	return output;
};

export const renderCacheSection = (): string => {
	return html`
		<am-form api="Config/update" auto>
			<p>$${App.text('systemCacheInfo')}</p>
			<p>
				${createField(
					'am-checkbox-large',
					null,
					{
						key: 'cache-enable',
						value: App.system.cache.enabled,
						name: 'cache-enable',
						label: App.text('systemCacheEnable'),
					},
					[],
					{ toggle: '#am-cache-settings' }
				).outerHTML}
			</p>
			<div id="am-cache-settings">
				<p>$${App.text('systemCacheMonitorInfo')}</p>
				<p>
					<am-select class="${classes.button}">
						$${App.text('systemCacheMonitor')}
						<span></span>
						<select name="monitor-delay">
							${renderOptions(
								[
									{ value: 60, text: '1 min' },
									{ value: 120, text: '2 min' },
									{ value: 300, text: '5 min' },
									{ value: 600, text: '10 min' },
								],
								App.system.cache.monitorDelay
							)}
						</select>
					</am-select>
				</p>
				<p>$${App.text('systemCacheLifetimeInfo')}</p>
				<p>
					<am-select class="${classes.button}">
						$${App.text('systemCacheLifetime')}
						<span></span>
						<select name="lifetime">
							${renderOptions(
								[
									{ value: 3600, text: '1 h' },
									{ value: 21600, text: '6 h' },
									{ value: 43200, text: '12 h' },
									{ value: 86400, text: '24 h' },
								],
								App.system.cache.lifetime
							)}
						</select>
					</am-select>
				</p>
				<am-form api="Cache/clear">
					<p>$${App.text('systemCacheClearInfo')}</p>
					<am-submit class="${classes.button}">
						$${App.text('systemCacheClear')}
					</am-submit>
				</am-form>
				<am-form api="Cache/purge">
					<p>$${App.text('systemCachePurgeInfo')}</p>
					<am-submit class="${classes.button}">
						$${App.text('systemCachePurge')}
						<span class="${classes.badge}">
							${App.system.tempDirectory}
						</span>
					</am-submit>
				</am-form>
			</div>
		</am-form>
	`;
};
