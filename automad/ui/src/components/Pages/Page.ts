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

import { App } from '../../core/app';
import { getTagFromRoute, routePage } from '../../core/router';
import { SidebarLayoutComponent } from './SidebarLayout';
import html from './Templates/Page.html';

/**
 * The page view.
 *
 * @extends SidebarLayoutComponent
 */
export class PageComponent extends SidebarLayoutComponent {
	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderMainPartial(): string {
		return html;
	}

	/**
	 * Render the save button partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderSaveButtonPartial(): string {
		return `<am-submit form="PageController::data">${App.text(
			'btn_save'
		)}</am-submit>`;
	}
}

customElements.define(getTagFromRoute(routePage), PageComponent);
