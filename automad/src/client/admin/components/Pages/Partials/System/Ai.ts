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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
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
 * Render the AI section.
 *
 * @returns the rendered HTML
 */
export const renderAiSection = (): string => {
	return html`
		<div class="${CSS.flex} ${CSS.flexColumn} ${CSS.flexGapLarge}">
			<am-form
				class="${CSS.flex} ${CSS.flexColumn} ${CSS.flexGapLarge}"
				${Attr.api}="${ConfigController.update}"
				${Attr.event}="${EventName.appStateRequireUpdate}"
				${Attr.auto}
			>
				<input type="hidden" name="type" value="ai" />
				<div>
					<p>${App.text('systemAiInfo')}</p>
					<am-ai-assistance-enable></am-ai-assistance-enable>
				</div>
			</am-form>
			<div class="am-ai-provider-setup">
				<p>${App.text('systemAiProviderInfo')}</p>
				<am-ai-provider-setup></am-ai-provider-setup>
			</div>
		</div>
	`;
};
