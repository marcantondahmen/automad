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
	 * Get the dark mode state.
	 */
	private get darkMode(): boolean {
		const localScheme = localStorage.getItem('color-scheme');

		if (localScheme) {
			return localScheme === 'dark';
		}

		return (
			window.matchMedia &&
			window.matchMedia('(prefers-color-scheme: dark)').matches
		);
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.render();

		listen(this, 'click', () => {
			localStorage.setItem(
				'color-scheme',
				!this.darkMode ? 'dark' : 'light'
			);

			document.documentElement.classList.add('am-u-no-transition');
			this.render();

			setTimeout(() => {
				document.documentElement.classList.remove('am-u-no-transition');
			}, 500);
		});
	}

	/**
	 * Render the toggle content.
	 */
	private render(): void {
		const darkMode = this.darkMode;

		query('.am-ui').classList.toggle('dark', darkMode);

		this.innerHTML = html`
			<i class="bi bi-${darkMode ? 'moon' : 'sun'}"></i>
			<span>${this.elementAttributes.text}</span>
		`;
	}
}

customElements.define('am-dark-mode-toggle', DarkModeToggleComponent);
