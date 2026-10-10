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
	Binding,
	createField,
	EventName,
	FieldTag,
} from '@/admin/core';
import { BaseComponent } from '../Base';

/**
 * A wrapper element for initializing the MCP server enable checkbox.
 *
 * @extends BaseComponent
 */
class McpEnableComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const mcpEnabled = new Binding('mcpEnabled', {
			initial: App.system.mcp.enabled,
		});

		this.listen(window, EventName.appStateChange, () => {
			mcpEnabled.value = App.system.mcp.enabled;
		});

		createField(
			FieldTag.toggleLarge,
			this,
			{
				key: 'mcpEnabled',
				value: App.system.mcp.enabled,
				name: 'mcpEnabled',
				label: App.text('systemMcp'),
				envKey: 'AM_MCP_SERVER_ENABLED',
			},
			[],
			{
				[Attr.toggle]: '.am-mcp-settings',
				[Attr.bind]: 'mcpEnabled',
				[Attr.bindTo]: 'checked',
			}
		);
	}
}

customElements.define('am-mcp-enable', McpEnableComponent);
