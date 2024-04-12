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
 * Copyright (c) 2021-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, CSS, html } from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';

/**
 * A simple alert box component.
 *
 * @example
 * <am-alert
 *     ${Attr.icon}="exclamation-circle"
 *     ${Attr.text}="pageNotFoundError"
 * ></am-alert>
 *
 * @extends BaseComponent
 */
class AlertComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return [Attr.icon, Attr.text];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const icon = this.elementAttributes[Attr.icon];
		const text = this.elementAttributes[Attr.text];

		this.classList.add(CSS.alert);

		this.innerHTML = html`
			<div class="${CSS.alertIcon}">
				<i class="bi bi-${icon || 'fire'}"></i>
			</div>
			<div class="${CSS.alertText}">${App.text(text) || text}</div>
		`;
	}
}

customElements.define('am-alert', AlertComponent);
