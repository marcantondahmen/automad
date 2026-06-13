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
 * Copyright (c) 2022-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import {
	App,
	Attr,
	create,
	CSS,
	EventName,
	fire,
	html,
	ImageCollectionController,
	queryAll,
	requestAPI,
} from '@/admin/core';
import { Image } from '@/admin/types';
import { BaseComponent } from '@/admin/components/Base';

/**
 * An image picker component.
 *
 * @example
 * <am-image-picker
 *     ${Attr.label}="Shared Files"
 * ></am-image-picker>
 * <am-image-picker
 *      ${Attr.page}="url"
 *      ${Attr.label}="Page Files"
 *      ${Attr.multiple}
 * ></am-image-picker>
 *
 * @extends BaseComponent
 */
class ImagePickerComponent extends BaseComponent {
	/**
	 * The wrapper element.
	 */
	private wrapper: HTMLElement;

	/**
	 * True if picker is a multi-select picker.
	 */
	private get isMultiSelect(): boolean {
		return this.hasAttribute(Attr.multiple);
	}

	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return [Attr.page, Attr.label];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.init();

		this.listen(
			window,
			EventName.filesChangeOnServer,
			this.init.bind(this)
		);
	}

	/**
	 * Request the file list and initialze the picker.
	 *
	 * @async
	 */
	private async init(): Promise<void> {
		this.classList.add(CSS.field);

		const header = create(
			'div',
			[CSS.flex, CSS.flexBetween, CSS.flexAlignCenter],
			{},
			this
		);

		if (this.elementAttributes[Attr.label]) {
			create(
				'label',
				[CSS.fieldLabel],
				{},
				header,
				this.elementAttributes[Attr.label]
			);

			this.removeAttribute(Attr.label);
		}

		this.renderSelectButtons(header);

		this.wrapper =
			this.wrapper ??
			create('div', [], {}, this, '<am-spinner></am-spinner>');

		const { data } = await requestAPI(ImageCollectionController.list, {
			url: this.elementAttributes[Attr.page] || '',
		});

		this.wrapper.innerHTML = '';

		const { images } = data;

		if (!images.length) {
			this.wrapper.innerHTML = html`
				<am-icon-text
					${Attr.icon}="folder-x"
					${Attr.text}="${App.text('noImagesFound')}"
				></am-icon-text>
			`;

			return;
		}

		this.renderGrid(images, this.wrapper);
	}

	/**
	 * Renders the select all/none buttons.
	 *
	 * @param container
	 */
	private renderSelectButtons(container: HTMLElement): void {
		if (!this.isMultiSelect) {
			return;
		}

		const buttons = create(
			'div',
			[CSS.flex, CSS.flexGap, CSS.flexAlignCenter],
			{},
			container
		);

		const selectAll = create(
			'span',
			[CSS.imagePickerSelectButton],
			{ [Attr.tooltip]: App.text('selectAll') },
			buttons,
			'<i class="bi bi-check-circle"></i>'
		);

		this.listen(selectAll, 'click', () => {
			queryAll<HTMLInputElement>('input[type="checkbox"]', this).forEach(
				(input) => {
					input.checked = true;
					fire('change', input);
				}
			);
		});

		const selectNone = create(
			'span',
			[CSS.imagePickerSelectButton],
			{ [Attr.tooltip]: App.text('selectNone') },
			buttons,
			'<i class="bi bi-x-circle"></i>'
		);

		this.listen(selectNone, 'click', () => {
			queryAll<HTMLInputElement>('input[type="checkbox"]', this).forEach(
				(input) => {
					input.checked = false;
					fire('change', input);
				}
			);
		});
	}

	/**
	 * Render the image grid.
	 *
	 * @param images
	 */
	private renderGrid(images: Image[], wrapper: HTMLElement) {
		const grid = create('div', [CSS.imagePicker], {}, wrapper);
		const base = this.elementAttributes[Attr.page] ? '' : '/shared/';

		images.forEach((image: Image) => {
			create(
				'label',
				[CSS.imagePickerItem],
				{ [Attr.tooltip]: image.name },
				grid,
				html`
					<input
						type="${this.isMultiSelect ? 'checkbox' : 'radio'}"
						name="${this.isMultiSelect
							? `${base}${image.name}`
							: 'selected'}"
						value="${base}${image.name}"
					/>
					<span><i class="bi bi-check"></i></span>
					<img src="${image.thumbnail}" />
				`
			);
		});
	}
}

customElements.define('am-image-picker', ImagePickerComponent);
