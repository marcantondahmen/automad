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
import { FileInfoComponent } from './FileInfo';

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
		let dimensions = '';
		let caption = '';

		if (file.width && file.height) {
			dimensions = html`
				<am-icon-text
					icon="aspect-ratio"
					text="${file.width} ✗ ${file.height}"
				></am-icon-text>
			`;
		}

		if (file.caption) {
			caption = html`
				<am-icon-text
					icon="text-left"
					text="${file.caption}"
				></am-icon-text>
			`;
		}

		this.innerHTML = html`
			${this.renderPreview(file)}
			<am-file-info
				class="${classes.cardBody} ${classes.flex} ${classes.flexColumn}"
			>
				<div
					class="${classes.cardTitle} ${classes.flexItemGrow}"
					title="$${file.basename}"
				>
					${file.basename}
				</div>
				<div>
					${caption} ${dimensions}
					<am-icon-text
						icon="calendar2-date"
						text="${file.mtime || '-'}"
					></am-icon-text>
					<am-icon-text
						icon="hdd"
						text="${file.size || '-'}"
					></am-icon-text>
				</div>
			</am-file-info>
			<div class="${classes.cardFooter}">
				${this.renderDropdown(file)}
				<span>
					<label>Delete</label>
					<input type="checkbox" name="delete[${file.basename}]" />
				</span>
			</div>
		`;

		queryAll('am-file-info', this).forEach((edit: FileInfoComponent) => {
			edit.data = file;
		});
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
				<am-file-robot
					file="${file.url}"
					class="${classes.cardImage}"
					title="$${file.basename}"
				>
					<img src="${file.thumbnail}" />
				</am-file-robot>
			`;
		}

		return html`<am-file-info></am-file-info>`;
	}

	/**
	 * Render the dropdown.
	 *
	 * @param file
	 * @returns the rendered dropdown
	 */
	private renderDropdown(file: File): string {
		let editImage = '';

		if (file.thumbnail) {
			editImage = html`
				<am-file-robot
					file="${file.url}"
					class="${classes.dropdownItem}"
				>
					<am-icon-text
						icon="pencil"
						text="${App.text('editImage')}"
					></am-icon-text>
				</am-file-robot>
			`;
		}

		return html`
			<am-dropdown>
				<i class="bi bi-three-dots-vertical"></i>
				<div class="${classes.dropdownItems}">
					${editImage}
					<am-file-info class="${classes.dropdownItem}">
						<am-icon-text
							icon="card-heading"
							text="${App.text('editFileInfo')}"
						></am-icon-text>
					</am-file-info>
					<am-copy
						class="${classes.dropdownItem}"
						value="${file.url}"
					>
						<am-icon-text
							icon="clipboard-plus"
							text="${App.text('copyUrlClipboard')}"
						></am-icon-text>
					</am-copy>
				</div>
			</am-dropdown>
		`;
	}
}

customElements.define('am-file-card', FileCardComponent);
