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
 * A feed state indicator component.
 *
 * @extends BaseIndicatorComponent
 */
class FeedIndicatorComponent extends BaseIndicatorComponent {
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

customElements.define('am-feed-indicator', FeedIndicatorComponent);
