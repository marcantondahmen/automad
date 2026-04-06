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

import { App, Binding, createField, EventName, FieldTag } from '@/admin/core';
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
		const debugBrowser = new Binding('debugBrowser', {
			initial: App.system.debug.browser,
		});

		this.listen(window, EventName.appStateChange, () => {
			debugBrowser.value = App.system.debug.browser;
		});

		createField(FieldTag.toggle, this, {
			key: 'debugBrowser',
			value: App.system.debug.browser,
			name: 'debugBrowser',
			label: App.text('systemDebugBrowser'),
			envKey: 'AM_DEBUG_BROWSER',
			hideLabel: true,
		});
	}
}

customElements.define('am-debug-browser', DebugBrowserComponent);
