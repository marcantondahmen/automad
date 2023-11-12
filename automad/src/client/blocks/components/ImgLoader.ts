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

import { create } from 'common';

/**
 * A simple image preloader with a blurred background.
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
		this.style.backgroundImage = `url(${this.getAttribute('preload')})`;

		const img = create(
			'img',
			[],
			{
				src: this.getAttribute('image'),
				width: this.getAttribute('width'),
				height: this.getAttribute('height'),
			},
			this
		);

		const loaded = () => {
			this.classList.add('am-loaded');

			setTimeout(() => {
				this.replaceWith(img);
			}, 300);
		};

		if (img.complete) {
			loaded();
		} else {
			img.addEventListener('load', loaded);
		}
	}
}

customElements.define('am-img-loader', ImgLoaderComponent);
