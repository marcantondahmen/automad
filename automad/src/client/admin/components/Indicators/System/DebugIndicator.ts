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
 * A debug state indicator component.
 *
 * @extends BaseActivationIndicatorComponent
 */
class SystemDebugIndicatorComponent extends BaseActivationIndicatorComponent {
	/**
	 * The enabled text.
	 */
	protected get textOn(): string {
		return App.text('debugEnabled');
	}

	/**
	 * The disabled text.
	 */
	protected get textOff(): string {
		return App.text('debugDisabled');
	}

	/**
	 * The state getter.
	 */
	protected get state(): boolean | number {
		return App.system.debug;
	}
}

customElements.define(
	'am-system-debug-indicator',
	SystemDebugIndicatorComponent
);
