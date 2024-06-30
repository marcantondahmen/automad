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
 * Copyright (c) 2023-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, CSS } from '@/admin/core';
import { BaseStateIndicatorComponent } from '../BaseStateIndicator';

/**
 * A debug state indicator component.
 *
 * @extends BaseStateIndicatorComponent
 */
class SystemMailIndicatorComponent extends BaseStateIndicatorComponent {
	/**
	 * Render the indicator.
	 */
	protected render(): void {
		this.innerHTML = `
			<am-icon-text 
				class="${
					App.system.mail.transport === 'smtp'
						? CSS.textActive
						: CSS.textMuted
				}" 
				${Attr.icon}="send" 
				${Attr.text}="${App.system.mail.transport}"
			></am-icon-text>
		`;
	}
}

customElements.define('am-system-mail-indicator', SystemMailIndicatorComponent);
