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

import { App } from '../core/app';
import { create } from '../core/create';
import { waitForPendingRequests } from '../core/request';
import { classes, query } from '../core/utils';
import { BaseComponent } from './Base';

type ViewName = 'Page' | 'System' | 'Shared' | 'Home' | 'Packages';

type Views = {
	[name in ViewName]: string;
};

const getViewSlug = (dashboard: string): ViewName => {
	const regex = new RegExp(`^${dashboard}\/`, 'i');

	return (window.location.pathname.replace(regex, '') as ViewName) || 'Home';
};

export const viewMap: Views = {
	Page: 'am-page',
	System: 'am-system',
	Shared: 'am-shared',
	Home: 'am-home',
	Packages: 'am-packages',
};

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
		return ['dashboard'];
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

		const slug = getViewSlug(this.elementAttributes.dashboard);
		const view = create(viewMap[slug], [], {}).init();

		this.progressBar(70);

		await waitForPendingRequests();

		let sidebar = query('am-sidebar');
		const sidebarScroll = sidebar?.scrollTop;

		if (typeof this.node !== 'undefined') {
			this.node.replaceWith(view);
		} else {
			this.appendChild(view);
		}

		this.node = view;

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
