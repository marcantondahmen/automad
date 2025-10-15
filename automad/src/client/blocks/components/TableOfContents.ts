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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { queryAll } from '@/common';

export default class TableOfContentsComponent {
	/**
	 * The main element.
	 */
	element: HTMLElement;

	/**
	 * Get the list type.
	 */
	private get listTag(): 'ol' | 'ul' {
		return this.element.getAttribute('type') == 'ordered' ? 'ol' : 'ul';
	}

	/**
	 * The class constructor.
	 */
	constructor(element: HTMLElement) {
		this.element = element;

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

		this.element.innerHTML = html;
	}
}
