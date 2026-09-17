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
import { Section } from '@/common';

/**
 * Render the MCP section.
 *
 * @returns the rendered HTML
 */
export const renderMcpSection = (): string => {
	const mcpUrl = App.system.mcp.url;

	return html`
		<am-form
			class="${CSS.flex} ${CSS.flexColumn} ${CSS.flexGapLarge}"
			${Attr.api}="${ConfigController.update}"
			${Attr.event}="${EventName.appStateRequireUpdate}"
			${Attr.auto}
		>
			<input type="hidden" name="type" value="mcp" />
			<div>
				<p>${App.text('systemMcpInfo')}</p>
				<am-mcp-enable></am-mcp-enable>
			</div>
			<div class="am-mcp-settings">
				<p>${App.text('systemMcpConnectInfo')}</p>
				<div class="${CSS.formGroup}">
					<input
						class="${CSS.input} ${CSS.flexItemGrow} ${CSS.formGroupItem}"
						value="${mcpUrl}"
						disabled
					/>
					<am-copy
						class="${CSS.button} ${CSS.buttonIcon} ${CSS.formGroupItem}"
						value="${mcpUrl}"
						${Attr.tooltip}="${App.text('copyUrlClipboard')}"
					>
						<i class="bi bi-clipboard"></i>
					</am-copy>
				</div>
				<p>${App.text('systemMcpConnectExample')}</p>
				<pre><code>claude mcp add --transport http automad ${mcpUrl} --header "Authorization: Bearer &lt;your-access-token&gt;"</code></pre>
				<p>
					${App.text('systemMcpAccessTokensHint')}
					<am-switcher-link ${Attr.section}="${Section.accessTokens}"
						>${App.text('systemAccessTokens')}</am-switcher-link
					>
				</p>
			</div>
		</am-form>
	`;
};
