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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	CSS,
	getTagFromRoute,
	html,
	PageTrashController,
	Route,
} from '@/admin/core';
import { BaseDashboardLayoutComponent } from './BaseDashboardLayout';

/**
 * The trash view.
 *
 * @extends BaseDashboardLayoutComponent
 */
export class TrashComponent extends BaseDashboardLayoutComponent {
	/**
	 * Set the page title that is used a document title suffix.
	 */
	protected get pageTitle(): string {
		return App.text('trashTitle');
	}

	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderMainPartial(): string {
		return html`
			<am-breadcrumbs-route
				${Attr.target}="${Route.trash}"
				${Attr.text}="${this.pageTitle}"
				${Attr.narrow}
			></am-breadcrumbs-route>
			<section
				class="${CSS.layoutDashboardSection} ${CSS.layoutDashboardSectionSticky}"
			>
				<div
					class="${CSS.layoutDashboardContent} ${CSS.layoutDashboardContentNarrow} ${CSS.layoutDashboardContentRow} ${CSS.flexGap}"
				>
					<am-filter placeholder="trashFilter"></am-filter>
					<am-form
						${Attr.api}="${PageTrashController.clear}"
						${Attr.confirm}="${App.text('trashClearConfirm')}"
					>
						<am-submit class="${CSS.button} ${CSS.buttonDanger}">
							${App.text('trashClear')}
						</am-submit>
					</am-form>
				</div>
			</section>
			<section class="${CSS.layoutDashboardSection}">
				<div
					class="${CSS.layoutDashboardContent} ${CSS.layoutDashboardContentNarrow}"
				>
					<am-trash-form></am-trash-form>
				</div>
			</section>
		`;
	}
}

customElements.define(getTagFromRoute(Route.trash), TrashComponent);
