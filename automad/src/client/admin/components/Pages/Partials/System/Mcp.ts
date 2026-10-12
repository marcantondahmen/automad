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
 * Render the connection command.
 *
 * @param name
 * @param mcpUrl
 * @param [token]
 * @returns the rendered command
 */
export const mcpConnectCmd = (
	name: string,
	mcpUrl: string,
	token: string = '<token>'
) => {
	const indent = ' '.repeat(2);

	return [
		'claude mcp add --transport http \\',
		`${indent}${name} \\`,
		`${indent}${mcpUrl} \\`,
		`${indent}--header "Authorization: Bearer ${token}"`,
	].join('\n');
};

/**
 * Render the connection JSON.
 *
 * @param name
 * @param mcpUrl
 * @param [token]
 * @returns the rendered JSON
 */
export const mcpConnectJson = (
	name: string,
	mcpUrl: string,
	token: string = '<token>'
) => {
	return JSON.stringify(
		{
			mcpServers: {
				[name]: {
					type: 'http',
					url: mcpUrl,
					headers: { Authorization: `Bearer ${token}` },
				},
			},
		},
		null,
		2
	);
};

/**
 * Render the MCP section.
 *
 * @returns the rendered HTML
 */
export const renderMcpSection = (): string => {
	const { name, url: mcpUrl, serverCardUrl } = App.system.mcp;

	const cliSnippet = mcpConnectCmd(name, mcpUrl);
	const jsonSnippet = mcpConnectJson(name, mcpUrl);

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
				<am-syntax ${Attr.lang}="none">$${mcpUrl}</am-syntax>
				<hr />
				<h2>${App.text('systemMcpAuthenticationHeading')}</h2>
				<p>${App.text('systemMcpAuthenticationInfo')}</p>
				<am-access-token-list></am-access-token-list>
				<hr />
				<h2>${App.text('systemMcpConnectHeading')}</h2>
				<p>${App.text('systemMcpConnectCli')}</p>
				<am-syntax ${Attr.lang}="none">$${cliSnippet}</am-syntax>
				<p>${App.text('systemMcpConnectJson')}</p>
				<am-syntax ${Attr.lang}="json">$${jsonSnippet}</am-syntax>
				<hr />
				<h2 class="${CSS.flex} ${CSS.flexGap}">
					${App.text('systemMcpServerCardHeading')}
					<i class="bi bi-card-heading"></i>
				</h2>
				<p>${App.text('systemMcpServerCardInfo')}</p>
				<a
					href="${serverCardUrl}"
					class="${CSS.button} ${CSS.buttonPrimary}"
					target="_blank"
				>
					${App.text('systemMcpServerCardOpen')}
				</a>
			</div>
		</am-form>
	`;
};
