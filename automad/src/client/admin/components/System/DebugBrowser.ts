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
 * Licensed under the MIT license.
 */

import { App, Binding, create, CSS, EventName, html } from '@/admin/core';
import { BaseComponent } from '../Base';

/**
 * A wrapper element for initializing the debug browser checkbox.
 *
 * @extends BaseComponent
 */
class DebugBrowserComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const debugEnabled = new Binding('debugBrowser', {
			initial: App.system.debug.browser,
		});

		this.listen(window, EventName.appStateChange, () => {
			debugEnabled.value = App.system.debug.browser;
		});

		create(
			'div',
			[CSS.toggle, CSS.toggleButton],
			{},
			this,
			html`
				<input
					type="checkbox"
					name="debugBrowser"
					id="am-debug-browser"
					value="1"
					${App.system.debug.browser ? 'checked' : ''}
				/>
				<label for="am-debug-browser">
					<i class="bi"></i>
					<span>${App.text('systemDebugBrowser')}</span>
				</label>
			`
		);
	}
}

customElements.define('am-debug-browser', DebugBrowserComponent);
