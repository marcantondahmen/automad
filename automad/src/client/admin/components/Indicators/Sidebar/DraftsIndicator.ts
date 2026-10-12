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
 * Copyright (c) 2021-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import {
	CSS,
	DraftCollectionController,
	EventName,
	html,
	requestApi,
} from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';

/**
 * A drafts count component.
 *
 * @extends BaseComponent
 */
class SidebarDraftsIndicatorComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.init();

		this.listen(
			window,
			`${EventName.appStateChange} ${EventName.contentSaved} ${EventName.contentPublished}`,
			this.init.bind(this)
		);
	}

	/**
	 * Render the state element.
	 *
	 * @async
	 */
	private async init(): Promise<void> {
		const { data } = await requestApi(DraftCollectionController.get);
		const count = data?.drafts?.length;

		this.classList.toggle(CSS.badge, count > 0);

		this.innerHTML = count ? count : '';
	}
}

customElements.define(
	'am-sidebar-drafts-indicator',
	SidebarDraftsIndicatorComponent
);
