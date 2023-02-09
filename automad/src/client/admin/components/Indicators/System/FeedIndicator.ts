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

import { App } from '../../../core';
import { BaseActivationIndicatorComponent } from '../BaseActivationIndicator';

/**
 * A feed state indicator component.
 *
 * @extends BaseActivationIndicatorComponent
 */
class SystemFeedIndicatorComponent extends BaseActivationIndicatorComponent {
	/**
	 * The enabled text.
	 */
	protected get textOn(): string {
		return App.text('feedEnabled');
	}

	/**
	 * The disabled text.
	 */
	protected get textOff(): string {
		return App.text('feedDisabled');
	}

	/**
	 * The state getter.
	 */
	protected get state(): boolean | number {
		return App.system.feed.enabled;
	}
}

customElements.define('am-system-feed-indicator', SystemFeedIndicatorComponent);
