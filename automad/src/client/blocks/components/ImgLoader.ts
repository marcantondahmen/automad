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
export default class ImgLoader {
	/**
	 * The class constructor.
	 */
	constructor(element: HTMLElement) {
		const img = create(
			'img',
			[],
			{
				src: element.getAttribute('image'),
				alt: element.getAttribute('alt') || '',
				width: element.getAttribute('width'),
				height: element.getAttribute('height'),
				loading: 'lazy',
			},
			element
		);

		if (element.hasAttribute('style')) {
			img.setAttribute('style', element.getAttribute('style'));
		}

		const loaded = () => {
			element.classList.add('am-loaded');

			setTimeout(() => {
				element.replaceWith(img);
			}, 300);
		};

		element.style.backgroundImage = `url(${element.getAttribute('preload')})`;

		if (img.complete) {
			loaded();
		} else {
			img.addEventListener('load', loaded);
		}
	}
}
