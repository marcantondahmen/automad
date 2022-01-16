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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { classes, text } from '../utils/core';
import { KeyValueMap } from '../utils/types';
import { BaseComponent } from './Base';

/**
 * A simple alert box component.
 *
 * @example
 * <am-alert
 * icon="exclamation-circle"
 * text="error_page_not_found"
 * type="danger"
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
		return ['icon', 'text', 'type'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const types: KeyValueMap = {
			danger: classes.alertDanger,
			success: classes.alertSuccess,
		};

		this.classList.add(classes.alert);

		if (this.elementAttributes.type) {
			this.classList.add(types[this.elementAttributes.type]);
		}

		this.innerHTML = `
			<div class="${classes.alertIcon}">
				<i class="bi bi-${this.elementAttributes.icon}"></i>
			</div>
			<div class="${classes.alertText}">
				${text(this.elementAttributes.text)}
			</div>
		`;
	}
}

customElements.define('am-alert', AlertComponent);
