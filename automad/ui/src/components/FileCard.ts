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

import { classes, html } from '../core';
import { File } from '../types';
import { BaseComponent } from './Base';

/**
 * A file card component.
 *
 * @example
 * <am-file-card></am-file-card>
 *
 * @extends BaseComponent
 */
class FileCardComponent extends BaseComponent {
	/**
	 * Set the file data and render the card.
	 *
	 * @param file
	 */
	set data(file: File) {
		this.render(file);
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(classes.card);
	}

	/**
	 * Render the file card.
	 *
	 * @param file
	 */
	render(file: File) {
		this.innerHTML = html`
			${this.renderPreview(file)}
			<div class="${classes.cardTitle}">$${file.basename}</div>
			<div class="${classes.cardBody}">$${file.caption || ''}</div>
			<div class="${classes.cardFooter}">
				<label>Delete</label>
				<input
					type="checkbox"
					name="delete[]"
					value="$${file.basename}"
				/>
			</div>
		`;
	}

	/**
	 * Render the preview for the file.
	 *
	 * @param file
	 * @returns the preview HTML
	 */
	renderPreview(file: File) {
		if (file.thumbnail) {
			return html`
				<div class="${classes.cardImage}">
					<img src="${file.thumbnail}" />
				</div>
			`;
		}

		return html``;
	}
}

customElements.define('am-file-card', FileCardComponent);
