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
	Binding,
	create,
	CSS,
	dateFormat,
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
import Tooltip from 'codex-tooltip';
import { BaseComponent } from '../Base';
import { SubmitComponent } from './Submit';

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
	 * The publish info tooltip.
	 */
	private tooltip: Tooltip;

	/**
	 * The publish button.
	 */
	private button: SubmitComponent;

	/**
	 * The last published timestamp.
	 */
	private lastPublished: string;

	/**
	 * The state binding.
	 */
	private stateBinding: Binding;

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

		this.stateBinding = new Binding('publicationState', {
			initial: App.pages[getPageURL()].publicationState,
		});

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

		this.button = query('am-submit', this);
		this.tooltip = new Tooltip();

		this.addListener(
			listen(this.button, 'mouseover', () => {
				if (this.tooltip) {
					this.tooltip.show(
						this.button,
						create(
							'span',
							[],
							{},
							null,
							`${App.text('lastPublished')}:<br>${dateFormat(this.lastPublished)}`
						),
						{}
					);
				}
			})
		);

		this.addListener(
			listen(this.button, 'mouseleave', () => {
				this.tooltip.hide();
			})
		);

		this.addListener(
			listen(
				window,
				`${EventName.contentSaved} ${EventName.contentPublished}`,
				this.update.bind(this)
			)
		);

		setTimeout(() => {
			this.update();
		}, 0);
	}

	/**
	 * Update the publish button.
	 */
	async update(): Promise<void> {
		const { data } = await requestAPI(this.stateController, {
			url: getPageURL(),
		});

		this.lastPublished = data.lastPublished;
		this.stateBinding.value = data.isPublished ? 'published' : 'draft';

		if (data.isPublished) {
			this.button.setAttribute('disabled', '');

			return;
		}

		if (this.button.hasAttribute('disabled')) {
			this.button.removeAttribute('disabled');
		}
	}
}

customElements.define(PublishComponent.TAG_NAME, PublishComponent);
