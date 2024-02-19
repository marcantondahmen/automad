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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { BaseComponent } from '@/components/Base';
import { AppController, html, requestAPI } from '@/core';

/**
 * A server info component.
 *
 * @extends BaseComponent
 */
class ServerInfoComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.init();
	}

	/**
	 * Request server info and render modal body.
	 */
	private async init(): Promise<void> {
		const { data } = await requestAPI(AppController.getServerInfo);

		this.innerHTML = html`
			<table>
				<tr>
					<td>Host Name</td>
					<td>${data.hostName}</td>
				</tr>
				<tr>
					<td>Host IP</td>
					<td>${data.hostIp}</td>
				</tr>
				<tr>
					<td>Server OS</td>
					<td>${data.serverOs}</td>
				</tr>
				<tr>
					<td>Server Software</td>
					<td>${data.serverSoftware}</td>
				</tr>
				<tr>
					<td>PHP Version</td>
					<td>${data.phpVersion}</td>
				</tr>
				<tr>
					<td>PHP Sapi</td>
					<td>${data.phpSapiName}</td>
				</tr>
				<tr>
					<td>Memory Limit</td>
					<td>${data.memoryLimit}</td>
				</tr>
			</table>
		`;
	}
}

customElements.define('am-server-info', ServerInfoComponent);
