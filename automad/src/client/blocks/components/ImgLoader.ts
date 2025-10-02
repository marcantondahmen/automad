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

import { create } from '@/common';

/**
 * A simple image preloader with a blurred background.
 *
 * @example
 * <am-img-loader image="..." preload="..." width="200" height="200"></am-img-loader>
 */
export class ImgLoaderComponent extends HTMLElement {
	/**
	 * The class constructor.
	 */
	constructor() {
		super();
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const img = create(
			'img',
			[],
			{
				src: this.getAttribute('image'),
				alt: this.getAttribute('alt') || '',
				width: this.getAttribute('width'),
				height: this.getAttribute('height'),
				loading: 'lazy',
			},
			this
		);

		if (this.hasAttribute('style')) {
			img.setAttribute('style', this.getAttribute('style'));
		}

		const loaded = () => {
			this.classList.add('am-loaded');

			setTimeout(() => {
				this.replaceWith(img);
			}, 300);
		};

		this.style.backgroundImage = `url(${this.getAttribute('preload')})`;

		if (img.complete) {
			loaded();
		} else {
			img.addEventListener('load', loaded);
		}
	}
}

customElements.define('am-img-loader', ImgLoaderComponent);
