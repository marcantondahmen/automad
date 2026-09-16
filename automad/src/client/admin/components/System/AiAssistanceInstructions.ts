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
 * A wrapper element for initializing the AI instructions editor.
 *
 * @extends BaseComponent
 */
class AiAssistanceInstructionsComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const aiAssitanceInstructions = new Binding('aiAssitanceInstructions', {
			initial: App.system.aiAssistance.instructions,
		});

		this.listen(window, EventName.appStateChange, () => {
			aiAssitanceInstructions.value =
				App.system.aiAssistance.instructions;
		});

		createField(
			FieldTag.code,
			this,
			{
				key: 'aiAssitanceInstructions',
				value: App.system.aiAssistance.instructions,
				name: 'aiAssitanceInstructions',
				hideLabel: true,
				placeholder:
					'Use a professional, concise, and friendly writing style.',
			},
			[],
			{
				[Attr.bind]: 'aiAssitanceInstructions',
				[Attr.bindTo]: 'value',
			}
		);
	}
}

customElements.define(
	'am-ai-assistance-instructions',
	AiAssistanceInstructionsComponent
);
