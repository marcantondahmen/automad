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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { create, CSS, html, listen } from '@/core';
import { Listener } from '@/types';
import { BaseComponent } from './Base';

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

		const img = create('img', [], {}, this);

		this.listener = listen(img, 'error', () => {
			this.innerHTML = html`
				<span class="${CSS.imgError}">
					<i class="bi bi-slash-circle"></i>
				</span>
			`;
		});

		img.src = value;
	}
}

customElements.define('am-img', ImgComponent);
