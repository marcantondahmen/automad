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
	EventName,
	getPageURL,
	getSlug,
	html,
	listen,
	PageController,
	query,
	requestAPI,
	Route,
	SharedController,
} from '@/core';
import { BaseComponent } from '../Base';

/**
 * The publish button and form for the navbar.
 *
 * @extends BaseComponent
 */
export class PublishComponent extends BaseComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 */
	static TAG_NAME = 'am-publish';

	/**
	 * The state controller route.
	 */
	private stateController: PageController | SharedController;

	/**
	 * The publish controller route.
	 */
	private publishController: PageController | SharedController;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		const route = getSlug() as Route;

		if (![Route.page, Route.shared].includes(route)) {
			this.remove();

			return;
		}

		const isPageRoute = route === Route.page;

		this.publishController = isPageRoute
			? PageController.publish
			: SharedController.publish;

		this.stateController = isPageRoute
			? PageController.getPublicationState
			: SharedController.getPublicationState;

		this.classList.add(CSS.navbarItem);

		this.innerHTML = html`
			<am-form
				${Attr.watch}
				${Attr.api}="${this.publishController}"
				${Attr.event}="${EventName.contentPublished}"
			>
				<am-submit disabled class="${CSS.button} ${CSS.buttonPrimary}">
					${App.text('publish')}
				</am-submit>
			</am-form>
		`;

		this.addListener(
			listen(window, EventName.contentSaved, this.update.bind(this))
		);

		this.update();
	}

	/**
	 * Update the publish button.
	 */
	async update(): Promise<void> {
		const { data } = await requestAPI(this.stateController, {
			url: getPageURL(),
		});

		const button = query('am-submit', this);

		if (data.isPublished) {
			button.setAttribute('disabled', '');

			return;
		}

		if (button.hasAttribute('disabled')) {
			button.removeAttribute('disabled');
		}
	}
}

customElements.define(PublishComponent.TAG_NAME, PublishComponent);
