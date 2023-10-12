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

import { App, Attr, ConfigController, CSS, EventName, html } from '@/core';

/**
 * Render the feed section.
 *
 * @returns the rendered HTML
 */
export const renderFeedSection = (): string => {
	return html`
		<am-form
			class="${CSS.flex} ${CSS.flexColumn} ${CSS.flexGapLarge}"
			${Attr.api}="${ConfigController.update}"
			${Attr.event}="${EventName.appStateRequireUpdate}"
			${Attr.auto}
		>
			<input type="hidden" name="type" value="feed" />
			<div>
				<p>${App.text('systemRssFeedInfo')}</p>
				<am-feed-enable></am-feed-enable>
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
				<am-feed-fields></am-feed-fields>
			</div>
		</am-form>
	`;
};
