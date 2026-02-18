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
 * Copyright (c) 2023-2026 by Marc Anton Dahmen
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
 * A wrapper element for initializing the debug enable checkbox.
 *
 * @extends BaseComponent
 */
class DebugEnableComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const debugEnabled = new Binding('debugEnabled', {
			initial: App.system.debug.enabled,
		});

		this.listen(window, EventName.appStateChange, () => {
			debugEnabled.value = App.system.debug.enabled;
		});

		createField(
			FieldTag.toggleLarge,
			this,
			{
				key: 'debugEnabled',
				value: App.system.debug.enabled,
				name: 'debugEnabled',
				label: App.text('systemDebugEnable'),
			},
			[],
			{
				[Attr.toggle]: '.am-debug-settings',
				[Attr.bind]: 'debugEnabled',
				[Attr.bindTo]: 'checked',
			}
		);
	}
}

customElements.define('am-debug-enable', DebugEnableComponent);
