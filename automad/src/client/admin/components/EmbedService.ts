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
import { create, CSS, html } from '../core';
import { EmbedServiceData } from '../types';

const services: EmbedServiceData = {
	twitter: {
		cls: ['twitter-tweet', 'tw-align-center'],
		script: 'https://platform.twitter.com/widgets.js',
		getId: (src) =>
			`https://platform.twitter.com/embed/Tweet.html?id=${src.split(/\//).pop()}`,
	},
	imgur: {
		cls: ['imgur-embed-pub'],
		script: 'https://s.imgur.com/min/embed.js',
		getId: (src) => src.replace(/^.+\/imgur.com\//, ''),
	},
};

/**
 * A twitter embed component.
 *
 * @extends BaseComponent
 */
class EmbedComponent extends BaseComponent {
	/**
	 * Get the type of embed.
	 */
	get type(): 'twitter' | 'imgur' {
		return this.getAttribute('type') as 'twitter' | 'imgur';
	}

	/**
	 * Get the source of the embed.
	 */
	get src(): string {
		return this.getAttribute('src');
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const data = services[this.type];

		if (!data) {
			return;
		}

		this.classList.add(CSS.flex, CSS.flexCenter);
		this.innerHTML = '';

		create(
			'blockquote',
			data.cls,
			{ 'data-id': data.getId(this.src) },
			this,
			html`<a href="${this.src}"></a>`
		);

		setTimeout(() => {
			create(
				'script',
				[],
				{ src: data.script, async: '', charset: 'utf-8' },
				this
			);
		}, 500);
	}
}

customElements.define('am-embed-service', EmbedComponent);
