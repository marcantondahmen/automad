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
 * Copyright (c) 2024 by Marc Anton Dahmen
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
	Route,
	SharedComponentController,
} from '@/admin/core';
import { newComponentButtonId } from '@/admin/components/Forms/SharedComponentsForm';
import { BaseDashboardLayoutComponent } from './BaseDashboardLayout';

/**
 * The components view.
 *
 * @extends BaseDashboardLayoutComponent
 */
export class SharedComponentsComponent extends BaseDashboardLayoutComponent {
	/**
	 * Set the page title that is used a document title suffix.
	 */
	protected get pageTitle(): string {
		return App.text('sharedComponentsTitle');
	}

	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderMainPartial(): string {
		return html`
			<am-breadcrumbs-route
				${Attr.target}="${Route.components}"
				${Attr.text}="${this.pageTitle}"
			></am-breadcrumbs-route>
			<section
				class="${CSS.layoutDashboardSection} ${CSS.layoutDashboardSectionSticky}"
			>
				<div
					class="${CSS.layoutDashboardContent} ${CSS.layoutDashboardContentRow} ${CSS.flexGap}"
				>
					<button
						id="${newComponentButtonId}"
						class="${CSS.button} ${CSS.buttonPrimary}"
					>
						${App.text('new')}
					</button>
					<am-filter placeholder="sharedComponentsFilter"></am-filter>
				</div>
			</section>
			<section class="${CSS.layoutDashboardSection}">
				<div class="${CSS.layoutDashboardContent}">
					<am-shared-component-form
						${Attr.api}="${SharedComponentController.data}"
					></am-shared-component-form>
				</div>
			</section>
		`;
	}

	/**
	 * Render an optional publish form.
	 *
	 * @returns the rendered HTML
	 */
	protected renderPublishForm(): string {
		return html`<am-shared-component-publish-form></am-shared-component-publish-form>`;
	}
}

customElements.define(
	getTagFromRoute(Route.components),
	SharedComponentsComponent
);
