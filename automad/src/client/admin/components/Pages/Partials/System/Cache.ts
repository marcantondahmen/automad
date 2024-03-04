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
	CacheController,
	ConfigController,
	CSS,
	EventName,
	html,
} from '@/admin/core';

/**
 * Render the cache section.
 *
 * @returns the rendered HTML
 */
export const renderCacheSection = (): string => {
	return html`
		<div class="${CSS.flex} ${CSS.flexColumn} ${CSS.flexGapLarge}">
			<am-form
				class="${CSS.flex} ${CSS.flexColumn} ${CSS.flexGapLarge}"
				${Attr.api}="${ConfigController.update}"
				${Attr.event}="${EventName.appStateRequireUpdate}"
				${Attr.auto}
			>
				<input type="hidden" name="type" value="cache" />
				<div>
					<p>${App.text('systemCacheInfo')}</p>
					<am-cache-enable></am-cache-enable>
				</div>
				<div class="am-cache-settings">
					<p>${App.text('systemCacheMonitorInfo')}</p>
					<am-cache-monitor></am-cache-monitor>
					<p>${App.text('systemCacheLifetimeInfo')}</p>
					<am-cache-lifetime></am-cache-lifetime>
				</div>
			</am-form>
			<am-form
				class="am-cache-settings"
				${Attr.api}="${CacheController.clear}"
			>
				<p>${App.text('systemCacheClearInfo')}</p>
				<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
					${App.text('systemCacheClear')}
				</am-submit>
			</am-form>
			<am-form
				class="am-cache-settings"
				${Attr.api}="${CacheController.purge}"
			>
				<p>${App.text('systemCachePurgeInfo')}</p>
				<am-submit class="${CSS.button}">
					${App.text('systemCachePurge')}
				</am-submit>
			</am-form>
		</div>
	`;
};
