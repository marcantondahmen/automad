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

import { App } from '@/admin/core';
import { BaseActivationIndicatorComponent } from '../BaseActivationIndicator';

/**
 * A access token indicator.
 *
 * @extends BaseActivationIndicatorComponent
 */
class AccessTokenIndicatorComponent extends BaseActivationIndicatorComponent {
	/**
	 * The enabled text.
	 */
	protected get textOn(): string {
		return `${App.text('systemAccessTokens')}: ${App.system.accessTokens.count}`;
	}

	/**
	 * The disabled text.
	 */
	protected get textOff(): string {
		return `${App.text('systemAccessTokens')}: 0`;
	}

	/**
	 * The state getter.
	 */
	protected get state(): number | boolean {
		return !!App.system.accessTokens.count;
	}
}

customElements.define(
	'am-access-token-indicator',
	AccessTokenIndicatorComponent
);
