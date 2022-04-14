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

import { App } from '../../core';
import { BaseIndicatorComponent } from './BaseIndicator';

/**
 * A cache state indicator component.
 *
 * @extends BaseIndicatorComponent
 */
class CacheIndicatorComponent extends BaseIndicatorComponent {
	/**
	 * The enabled text.
	 */
	protected get textOn(): string {
		return App.text('cacheEnabled');
	}

	/**
	 * The disabled text.
	 */
	protected get textOff(): string {
		return App.text('cacheDisabled');
	}

	/**
	 * The state getter.
	 */
	protected get state(): boolean | number {
		return App.system.cache.enabled;
	}
}

customElements.define('am-cache-indicator', CacheIndicatorComponent);
