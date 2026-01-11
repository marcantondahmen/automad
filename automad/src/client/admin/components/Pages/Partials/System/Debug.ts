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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	ConfigController,
	CSS,
	EventName,
	html,
} from '@/admin/core';

/**
 * Render the debug section.
 *
 * @returns the rendered HTML
 */
export const renderDebugSection = (): string => {
	return html`
		<am-form
			class="${CSS.flex} ${CSS.flexColumn} ${CSS.flexGapLarge}"
			${Attr.api}="${ConfigController.update}"
			${Attr.event}="${EventName.appStateRequireUpdate}"
			${Attr.auto}
		>
			<input type="hidden" name="type" value="debug" />
			<div>
				<p>${App.text('systemDebugInfo')}</p>
				<am-debug-enable></am-debug-enable>
			</div>
			<div class="am-debug-settings">
				<p>${App.text('systemDebugFileHelp')}</p>
				<pre><code>tail -n +1 -F $(php automad/console log:path)</code></pre>
				<p>${App.text('systemDebugBrowserHelp')}</p>
				<am-debug-browser></am-debug-browser>
			</div>
		</am-form>
	`;
};
