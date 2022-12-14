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
 * Copyright (c) 2021-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { Attr, CSS, html } from '../../core';
import { BaseStateIndicatorComponent } from './BaseStateIndicator';

/**
 * A state indicator component.
 *
 * @extends BaseStateIndicatorComponent
 */
export abstract class BaseActivationIndicatorComponent extends BaseStateIndicatorComponent {
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
					class="${CSS.textActive}"
					${Attr.icon}="check-circle"
					${Attr.text}="${this.textOn}"
				></am-icon-text>
			`;
		} else {
			this.innerHTML = html`
				<am-icon-text
					class="${CSS.textMuted}"
					${Attr.icon}="slash-circle"
					${Attr.text}="${this.textOff}"
				></am-icon-text>
			`;
		}
	}
}
