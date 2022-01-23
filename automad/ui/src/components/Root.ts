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

import { App } from '../utils/app';
import { create } from '../utils/create';
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

	private async init(): Promise<void> {
		await App.bootstrap(this);

		this.updateView();
	}

	async updateView(): Promise<void> {
		await App.updateState();

		const slug = getViewSlug(this.elementAttributes.dashboard);
		const view = await create(viewMap[slug], [], {}).init();

		this.innerHTML = '';
		this.appendChild(view);
	}
}

customElements.define('am-root', RootComponent);
