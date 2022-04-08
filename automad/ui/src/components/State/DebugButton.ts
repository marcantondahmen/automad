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

import { App, classes, html, Routes } from '../../core';
import { Sections } from '../Switcher/Switcher';
import { BaseStateComponent } from './BaseState';

/**
 * A debug state component.
 *
 * @extends BaseComponent
 */
class DebugButtonComponent extends BaseStateComponent {
	/**
	 * Render the state element.
	 */
	render(): void {
		if (App.system.debug) {
			this.innerHTML = html`
				<am-link
					class="${classes.button}"
					target="${Routes.system}?section=${Sections.debug}"
				>
					<am-icon-text
						icon="bug"
						text="${App.text('systemDebug')}"
					></am-icon-text>
				</am-link>
			`;
		} else {
			this.innerHTML = '';
		}
	}
}

customElements.define('am-debug-button', DebugButtonComponent);
