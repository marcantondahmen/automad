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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	classes,
	App,
	getValidRouteOrRedirect,
	create,
	getTagFromRoute,
	waitForPendingRequests,
	query,
} from '../core';
import { BaseComponent } from './Base';

/**
 * The root app component.
 *
 * @extends BaseComponent
 */
export class RootComponent extends BaseComponent {
	/**
	 * The main child of the root element that is basically a rendered view.
	 */
	private node: HTMLElement;

	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['base'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.init();
	}

	/**
	 * Init the root component.
	 */
	private async init(): Promise<void> {
		this.classList.add(classes.root);
		this.progressBar(10);

		await App.bootstrap(this);

		this.updateView();
	}

	/**
	 * Update the root component.
	 */
	async updateView(): Promise<void> {
		this.progressBar(25);

		await App.updateState();

		this.progressBar(50);

		const route = getValidRouteOrRedirect();
		const page = create(getTagFromRoute(route), [], {}).init();

		this.progressBar(70);

		await waitForPendingRequests();

		let sidebar = query('am-sidebar');
		const sidebarScroll = sidebar?.scrollTop;

		if (typeof this.node !== 'undefined') {
			this.node.replaceWith(page);
		} else {
			this.appendChild(page);
		}

		this.node = page;

		sidebar = query('am-sidebar');

		if (sidebar && sidebarScroll) {
			sidebar.scrollTop = sidebarScroll;
		}

		this.progressBar(100);

		setTimeout(() => {
			query('html').scrollTop = 0;

			this.progressBar(0);
		}, 0);
	}

	/**
	 * Control the progress bar display.
	 *
	 * @param progress
	 */
	private progressBar(progress: number): void {
		this.classList.toggle(classes.rootLoading, progress > 0);

		if (progress > 0) {
			this.style.setProperty('--progress', `${progress}%`);

			return;
		}

		setTimeout(() => {
			this.style.setProperty('--progress', `${progress}%`);
		}, 500);
	}
}

customElements.define('am-root', RootComponent);
