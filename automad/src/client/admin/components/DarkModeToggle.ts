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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { html, listen, query } from '../core';
import { BaseComponent } from './Base';

/**
 * A spinner component.
 *
 * @extends BaseComponent
 */
class DarkModeToggleComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['text'];
	}

	/**
	 * The saved dark mode state.
	 */
	private darkMode: boolean = false;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const localScheme = localStorage.getItem('color-scheme');
		this.darkMode = localScheme === 'dark';

		if (!localScheme) {
			this.darkMode =
				window.matchMedia &&
				window.matchMedia('(prefers-color-scheme: dark)').matches;
		}

		query('.am-ui, html').classList.toggle('dark', this.darkMode);

		listen(this, 'click', () => {
			this.darkMode = !this.darkMode;

			localStorage.setItem(
				'color-scheme',
				this.darkMode ? 'dark' : 'light'
			);

			document.documentElement.classList.add('am-u-no-transition');
			query('.am-ui, html').classList.toggle('dark', this.darkMode);

			this.render();

			setTimeout(() => {
				document.documentElement.classList.remove('am-u-no-transition');
			}, 500);
		});

		this.render();
	}

	/**
	 * Render the toggle content.
	 */
	private render(): void {
		this.innerHTML = html`
			<i class="bi bi-${this.darkMode ? 'moon' : 'sun'}"></i>
			<span>${this.elementAttributes.text}</span>
		`;
	}
}

customElements.define('am-dark-mode-toggle', DarkModeToggleComponent);
