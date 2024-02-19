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
 * Copyright (c) 2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { queryAll } from 'common';

class TableOfContentsComponent extends HTMLElement {
	/**
	 * The tag name.
	 *
	 * @static
	 */
	static TAG_NAME = 'am-table-of-contents';

	/**
	 * The class constructor.
	 */
	constructor() {
		super();
	}

	/**
	 * Get the list type.
	 */
	private get listTag(): 'ol' | 'ul' {
		return this.getAttribute('type') == 'ordered' ? 'ol' : 'ul';
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		if (document.readyState === 'loading') {
			document.addEventListener(
				'DOMContentLoaded',
				this.render.bind(this)
			);

			return;
		}

		this.render();
	}

	/**
	 * Render the actual table of contents.
	 */
	private render(): void {
		let open = 0;
		let lastLevel = 1;
		let html = '';

		const items = queryAll('h2[id], h3[id], h4[id]');

		items.forEach((item) => {
			const level = parseInt(item.tagName.replace(/h/i, ''));

			if (level > lastLevel) {
				const diff = level - lastLevel;

				for (let n = 1; n <= diff; n++) {
					open++;
					html += `<${this.listTag}><li>`;
				}
			}

			if (level < lastLevel) {
				const diff = lastLevel - level;

				for (let n = 1; n <= diff; n++) {
					open--;
					html += `</li></${this.listTag}>`;
				}
			}

			if (level <= lastLevel) {
				html += `</li><li>`;
			}

			html += `<a href="#${item.id}">${item.textContent}</a>`;
			lastLevel = level;
		});

		for (var i = 1; i <= open; i++) {
			html += `</li></${this.listTag}>`;
		}

		this.innerHTML = html;
	}
}

customElements.define(
	TableOfContentsComponent.TAG_NAME,
	TableOfContentsComponent
);
