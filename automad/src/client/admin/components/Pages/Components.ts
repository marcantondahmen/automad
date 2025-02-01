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

import {
	App,
	Attr,
	CSS,
	getTagFromRoute,
	html,
	Route,
	ComponentController,
} from '@/admin/core';
import { newComponentButtonClass } from '@/admin/components/Forms/ComponentCollectionForm';
import { BaseDashboardLayoutComponent } from './BaseDashboardLayout';
import { ComponentEditorComponent } from '../ComponentEditor';

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
				class="${CSS.componentsStickySection} ${CSS.layoutDashboardSection} ${CSS.layoutDashboardSectionSticky}"
			>
				<div
					class="${CSS.layoutDashboardContent} ${CSS.layoutDashboardContentRow} ${CSS.flexGap}"
				>
					<am-filter
						placeholder="componentFilter"
						${Attr.target}="${ComponentEditorComponent.TAG_NAME}"
					></am-filter>
					<button
						class="${newComponentButtonClass} ${CSS.button} ${CSS.buttonPrimary}"
					>
						${App.text('add')}
					</button>
				</div>
			</section>
			<section class="${CSS.layoutDashboardSection}">
				<div class="${CSS.layoutDashboardContent}">
					<div class="${CSS.componentsHint}">
						<p>${App.text('componentsHint')}</p>
						<button
							class="${newComponentButtonClass} ${CSS.button} ${CSS.buttonPrimary}"
						>
							${App.text('newComponent')}
						</button>
					</div>
					<am-component-collection-form
						${Attr.api}="${ComponentController.data}"
					></am-component-collection-form>
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
