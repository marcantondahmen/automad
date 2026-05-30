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
 * A wrapper element for initializing the AI enable checkbox.
 *
 * @extends BaseComponent
 */
class AiAssistanceEnableComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const aiAssitanceEnabled = new Binding('aiAssitanceEnabled', {
			initial: App.system.ai.enabled,
		});

		this.listen(window, EventName.appStateChange, () => {
			aiAssitanceEnabled.value = App.system.ai.enabled;
		});

		createField(
			FieldTag.toggleLarge,
			this,
			{
				key: 'aiAssitanceEnabled',
				value: App.system.ai.enabled,
				name: 'aiAssitanceEnabled',
				label: App.text('systemAi'),
				envKey: 'AM_AI_ASSISTANCE_ENABLED',
			},
			[],
			{
				[Attr.toggle]: '.am-ai-provider-setup',
				[Attr.bind]: 'aiAssitanceEnabled',
				[Attr.bindTo]: 'checked',
			}
		);
	}
}

customElements.define('am-ai-assistance-enable', AiAssistanceEnableComponent);
