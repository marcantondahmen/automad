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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { getTagFromRoute, Route } from '@/admin/core';
import { Partials } from '@/admin/types';
import { BaseLayoutComponent } from './BaseLayout';
import { inPage } from './Templates/InPageLayoutTemplate';

class InPageComponent extends BaseLayoutComponent {
	/**
	 * The template render function used to render the view.
	 */
	protected template: Function = inPage;

	/**
	 * Set the page title that is used a document title suffix.
	 */
	protected get pageTitle(): string {
		return 'InPage';
	}

	/**
	 * An array of partials that must be provided in order to render partial references.
	 */
	protected partials: Partials = {
		main: this.renderMainPartial(),
	};

	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 */
	private renderMainPartial(): string {
		return '<am-inpage-form></am-inpage-form>';
	}
}

customElements.define(getTagFromRoute(Route.inpage), InPageComponent);
