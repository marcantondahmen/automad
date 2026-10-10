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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import {
	App,
	Attr,
	CSS,
	DraftCollectionController,
	EventName,
	getTagFromRoute,
	html,
	routes,
} from '@/admin/core';
import { DraftCardComponent } from '@/admin/components/Publish/DraftCard';
import { BaseDashboardLayoutComponent } from './BaseDashboardLayout';

/**
 * The drafts view.
 *
 * @extends BaseDashboardLayoutComponent
 */
class DraftsComponent extends BaseDashboardLayoutComponent {
	/**
	 * Set the page title that is used a document title suffix.
	 */
	protected get pageTitle(): string {
		return App.text('draftsTitle');
	}

	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderMainPartial(): string {
		return html`
			<am-breadcrumbs-route
				${Attr.target}="${routes.drafts}"
				${Attr.text}="${this.pageTitle}"
			></am-breadcrumbs-route>
			<section
				class="${CSS.layoutDashboardSection} ${CSS.layoutDashboardSectionSticky}"
			>
				<div
					class="${CSS.layoutDashboardContent} ${CSS.layoutDashboardContentRow} ${CSS.flexGap}"
				>
					<am-filter
						placeholder="draftsFilter"
						${Attr.target}="${DraftCardComponent.TAG_NAME}"
					></am-filter>
					<am-form
						${Attr.api}="${DraftCollectionController.publishAll}"
						${Attr.event}="${EventName.appStateRequireUpdate}"
					>
						<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
							${App.text('draftsPublishAll')}
						</am-submit>
					</am-form>
				</div>
			</section>
			<section class="${CSS.layoutDashboardSection}">
				<div class="${CSS.layoutDashboardContent}">
					<am-draft-list></am-draft-list>
				</div>
			</section>
		`;
	}
}

customElements.define(getTagFromRoute(routes.drafts), DraftsComponent);
