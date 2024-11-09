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
	listen,
	PageController,
	requestAPI,
	Route,
	SharedController,
} from '@/admin/core';
import Tooltip from 'codex-tooltip';
import { BaseComponent } from '../Base';
import { SubmitComponent } from './Submit';

const enable = (button: SubmitComponent): void => {
	if (button.hasAttribute('disabled')) {
		button.removeAttribute('disabled');
	}

	button.classList.remove(CSS.textMuted);
};

const disable = (button: SubmitComponent): void => {
	button.setAttribute('disabled', '');
	button.classList.add(CSS.textMuted);
};

/**
 * The publish button and form for the navbar.
 *
 * @extends BaseComponent
 */
export class PublishFormComponent extends BaseComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 */
	static TAG_NAME = 'am-publish-form';

	/**
	 * The state controller route.
	 */
	private stateController: PageController | SharedController;

	/**
	 * The discard controller route.
	 */
	private discardController: PageController | SharedController;

	/**
	 * The publish controller route.
	 */
	private publishController: PageController | SharedController;

	/**
	 * The publish info tooltip.
	 */
	private tooltip: Tooltip;

	/**
	 * The discard button.
	 */
	private discardButton: SubmitComponent;

	/**
	 * The publish button.
	 */
	private publishButton: SubmitComponent;

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
		const pageUrl = getPageURL();

		this.stateBinding = new Binding('publicationState', {
			initial: pageUrl ? App.pages[pageUrl].publicationState : null,
		});

		this.discardController = isPageRoute
			? PageController.discardDraft
			: SharedController.discardDraft;

		this.publishController = isPageRoute
			? PageController.publish
			: SharedController.publish;

		this.stateController = isPageRoute
			? PageController.getPublicationState
			: SharedController.getPublicationState;

		this.classList.add(CSS.navbarGroup);

		const discardForm = create(
			'am-form',
			[CSS.displaySmallNone],
			{
				[Attr.watch]: '',
				[Attr.confirm]: App.text('discardDraftConfirm'),
				[Attr.api]: this.discardController,
				[Attr.event]: EventName.contentPublished,
			},
			this
		);

		this.discardButton = create(
			'am-submit',
			[CSS.navbarItem, CSS.navbarItemGlyph, CSS.textMuted],
			{ disabled: '', [Attr.tooltip]: App.text('discardDraftTooltip') },
			discardForm,
			'↺'
		);

		const publishForm = create(
			'am-form',
			[],
			{
				[Attr.watch]: '',
				[Attr.api]: this.publishController,
				[Attr.event]: EventName.contentPublished,
			},
			this
		);

		this.publishButton = create(
			'am-submit',
			[CSS.button],
			{ disabled: '' },
			publishForm,
			App.text('publish')
		);

		this.tooltip = new Tooltip();

		this.addListener(
			listen(this.publishButton, 'mouseover', () => {
				if (this.tooltip && this.lastPublished) {
					this.tooltip.show(
						this.publishButton,
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
			listen(this.publishButton, 'mouseleave', () => {
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
			disable(this.discardButton);
			disable(this.publishButton);

			return;
		}

		if (data.lastPublished) {
			enable(this.discardButton);
		}

		enable(this.publishButton);
	}
}

customElements.define(PublishFormComponent.TAG_NAME, PublishFormComponent);
