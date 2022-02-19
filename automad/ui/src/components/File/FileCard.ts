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

import { App, classes, html, queryAll } from '../../core';
import { File } from '../../types';
import { BaseComponent } from '../Base';
import { FileEditComponent } from './FileEdit';

/**
 * A file card component.
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
	private render(file: File): void {
		this.innerHTML = html`
			${this.renderPreview(file)}
			<div class="${classes.cardTitle}">${file.basename}</div>
			<div class="${classes.cardBody}">$${file.caption || ''}</div>
			<div class="${classes.cardFooter}">
				${this.renderDropdown(file)}
				<span>
					<label>Delete</label>
					<input type="checkbox" name="delete[${file.basename}]" />
				</span>
			</div>
		`;

		queryAll('am-file-edit, am-image-copy-resized', this).forEach(
			(edit: FileEditComponent) => {
				edit.data = file;
			}
		);
	}

	/**
	 * Render the preview for the file.
	 *
	 * @param file
	 * @returns the preview HTML
	 */
	private renderPreview(file: File): string {
		if (file.thumbnail) {
			return html`
				<am-file-edit class="${classes.cardImage}">
					<img src="${file.thumbnail}" />
				</am-file-edit>
			`;
		}

		return html`<am-file-edit></am-file-edit>`;
	}

	/**
	 * Render the dropdown.
	 *
	 * @param file
	 * @returns the rendered dropdown
	 */
	private renderDropdown(file: File): string {
		let resizeButton = '';

		if (file.thumbnail) {
			resizeButton = html`
				<am-image-copy-resized class="${classes.dropdownItem}">
					<i class="bi bi-crop"></i>
					<span>${App.text('btn_copy_resized')}</span>
				</am-image-copy-resized>
			`;
		}

		return html`
			<am-dropdown>
				<i class="bi bi-three-dots-vertical"></i>
				<div class="${classes.dropdownItems}">
					${resizeButton}
					<am-file-edit class="${classes.dropdownItem}">
						<i class="bi bi-pencil"></i>
						<span>${App.text('btn_edit_file_info')}</span>
					</am-file-edit>
					<am-copy
						class="${classes.dropdownItem}"
						value="${file.url}"
					>
						<i class="bi bi-clipboard-plus"></i>
						<span>${App.text('btn_copy_url_clipboard')}</span>
					</am-copy>
				</div>
			</am-dropdown>
		`;
	}
}

customElements.define('am-file-card', FileCardComponent);
