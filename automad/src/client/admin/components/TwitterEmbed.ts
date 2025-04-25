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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { BaseComponent } from '@/admin/components/Base';
import { create, CSS } from '../core';

/**
 * A twitter embed component.
 *
 * @extends BaseComponent
 */
class TwitterEmbedComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const src = this.getAttribute('src');
		const id = src.split(/\//).pop();

		this.classList.add(CSS.flex, CSS.flexCenter);

		create(
			'iframe',
			[],
			{
				src: `https://platform.twitter.com/embed/Tweet.html?id=${id}`,
				width: 550,
				height: 290,
				frameborder: '0',
				scrolling: 'no',
				allowtransparency: 'true',
			},
			this
		);
	}
}

customElements.define('am-twitter-embed', TwitterEmbedComponent);
