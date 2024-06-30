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
 * Copyright (c) 2021-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App } from '@/admin/core';
import { BaseActivationIndicatorComponent } from '@/admin/components/Indicators/BaseActivationIndicator';

/**
 * A cache state indicator component.
 *
 * @extends BaseActivationIndicatorComponent
 */
class SystemCacheIndicatorComponent extends BaseActivationIndicatorComponent {
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

customElements.define(
	'am-system-cache-indicator',
	SystemCacheIndicatorComponent
);
