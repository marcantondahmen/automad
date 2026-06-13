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
				<input type="hidden" name="type" value="aiEnabled" />
				<div>
					<p>${App.text('systemAiInfo')}</p>
					<am-ai-assistance-enable></am-ai-assistance-enable>
				</div>
			</am-form>
			<div class="am-ai-setup">
				<p>${App.text('systemAiProviderText')}</p>
				<am-ai-provider-setup></am-ai-provider-setup>
				<h2>${App.text('systemAiInstructionsHeading')}</h2>
				<p>${App.text('systemAiInstructionsText')}</p>
				<am-form
					class="${CSS.flex} ${CSS.flexColumn} ${CSS.flexGap}"
					${Attr.api}="${ConfigController.update}"
					${Attr.watch}
				>
					<input type="hidden" name="type" value="aiInstructions" />
					<am-ai-assistance-instructions></am-ai-assistance-instructions>
					<div>
						<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
							${App.text('save')}
						</am-submit>
					</div>
				</am-form>
			</div>
		</div>
	`;
};
