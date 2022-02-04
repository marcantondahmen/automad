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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { Partials } from '../../types';
import { BaseLayoutComponent } from './BaseLayout';
import { centered } from './Templates/CenteredLayoutTemplate';

/**
 * The Automad base component. All Automad components are based on this class.
 *
 * @extends BaseLayoutComponent
 */
export abstract class CenteredLayoutComponent extends BaseLayoutComponent {
	/**
	 * The template render function used to render the view.
	 */
	protected render: Function = centered;

	/**
	 * An array of partials that must be provided in order to render partial references.
	 */
	protected partials: Partials = {
		main: this.renderMainPartial(),
		title: this.renderTitlePartial(),
	};

	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderMainPartial(): string {
		return '';
	}

	/**
	 * Render the navbar title partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderTitlePartial(): string {
		return '';
	}
}
