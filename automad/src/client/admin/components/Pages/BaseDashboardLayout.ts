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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { Partials } from '@/admin/types';
import { BaseLayoutComponent } from './BaseLayout';
import { dashboardLayout } from './Templates/DashboardLayoutTemplate';

/**
 * The Automad base component. All Automad components are based on this class.
 *
 * @extends BaseLayoutComponent
 */
export abstract class BaseDashboardLayoutComponent extends BaseLayoutComponent {
	/**
	 * The template render function used to render the view.
	 */
	protected template: Function = dashboardLayout;

	/**
	 * An array of partials that must be provided in order to render partial references.
	 */
	protected partials: Partials = {
		main: this.renderMainPartial(),
		publishForm: this.renderPublishForm(),
	};

	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 * @abstract
	 */
	protected abstract renderMainPartial(): string;

	/**
	 * Render an optional publish form.
	 *
	 * @returns the rendered HTML
	 */
	protected renderPublishForm(): string {
		return '';
	}
}
