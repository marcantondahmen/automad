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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
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
 * A wrapper element for initializing the i18n enable checkbox.
 *
 * @extends BaseComponent
 */
class I18nEnableComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const i18nEnabled = new Binding('i18nEnabled', {
			initial: App.system.i18n,
		});

		this.listen(window, EventName.appStateChange, () => {
			i18nEnabled.value = App.system.i18n;
		});

		createField(
			FieldTag.toggleLarge,
			this,
			{
				key: 'i18nEnabled',
				value: App.system.i18n,
				name: 'i18nEnabled',
				label: App.text('systemI18nEnable'),
			},
			[],
			{
				[Attr.bind]: 'i18nEnabled',
				[Attr.bindTo]: 'checked',
			}
		);
	}
}

customElements.define('am-i18n-enable', I18nEnableComponent);
