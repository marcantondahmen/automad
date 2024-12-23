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
	ComponentController,
} from '@/admin/core';
import { newComponentButtonId } from '@/admin/components/Forms/ComponentsForm';
import { BaseDashboardLayoutComponent } from './BaseDashboardLayout';

/**
 * The components view.
 *
 * @extends BaseDashboardLayoutComponent
 */
class ComponentsComponent extends BaseDashboardLayoutComponent {
	/**
	 * Set the page title that is used a document title suffix.
	 */
	protected get pageTitle(): string {
		return App.text('componentsTitle');
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
					<am-filter placeholder="componentFilter"></am-filter>
				</div>
			</section>
			<section class="${CSS.layoutDashboardSection}">
				<div class="${CSS.layoutDashboardContent}">
					<am-components-form
						${Attr.api}="${ComponentController.data}"
					></am-components-form>
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
		return html`<am-component-publish-form></am-component-publish-form>`;
	}
}

customElements.define(getTagFromRoute(Route.components), ComponentsComponent);
