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

import { classes, html } from '../../core';
import { BaseStateComponent } from './BaseState';

/**
 * A state indicator component.
 *
 * @extends BaseComponent
 */
export abstract class BaseActivationIndicatorComponent extends BaseStateComponent {
	/**
	 * The enabled text.
	 */
	protected abstract get textOn(): string;

	/**
	 * The disabled text.
	 */
	protected abstract get textOff(): string;

	/**
	 * The state getter.
	 */
	protected abstract get state(): boolean | number;

	/**
	 * Render the state element.
	 */
	protected render(): void {
		if (this.state) {
			this.innerHTML = html`
				<am-icon-text
					class="${classes.textSuccess}"
					icon="check-circle-fill"
					text="${this.textOn}"
				></am-icon-text>
			`;
		} else {
			this.innerHTML = html`
				<am-icon-text
					class="${classes.textMuted}"
					icon="slash-circle-fill"
					text="${this.textOff}"
				></am-icon-text>
			`;
		}
	}
}
