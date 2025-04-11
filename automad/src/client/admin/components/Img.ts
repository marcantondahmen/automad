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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { create, CSS, html, listen } from '@/admin/core';
import { Listener } from '@/admin/types';
import { BaseComponent } from './Base';

/**
 * A basic image component with error handling.
 *
 * @extends BaseComponent
 */
export class ImgComponent extends BaseComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 */
	static TAG_NAME = 'am-img';

	/**
	 * The src setter.
	 *
	 * @param value
	 */
	set src(value: string) {
		this.render(value);
	}

	/**
	 * The internal error listener.
	 */
	private listener: Listener;

	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['src'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.img);
	}

	/**
	 * The callback that is used when attributes are changed or on initialization.
	 *
	 * @param name
	 * @param oldValue
	 * @param newValue
	 */
	attributeChangedCallback(
		name: string,
		oldValue: string,
		newValue: string
	): void {
		if (name === 'src') {
			this.src = newValue;
		}
	}

	/**
	 * Render the element.
	 */
	private render(value: string): void {
		this.innerHTML = '';
		this.listener?.remove();

		if (value.length == 0) {
			this.innerHTML = this.placeholder('slash-circle');

			return;
		}

		const img = create('img', [], { referrerpolicy: 'no-referrer' }, this);

		this.listener = listen(img, 'error', () => {
			this.innerHTML = this.placeholder('exclamation-triangle');
		});

		img.src = value;
	}

	/**
	 * Render a placeholder.
	 *
	 * @param icon
	 * @return the rendered HTML
	 */
	private placeholder(icon: string): string {
		return html`
			<span class="${CSS.imgError}">
				<i class="bi bi-${icon}"></i>
			</span>
		`;
	}
}

customElements.define('am-img', ImgComponent);
