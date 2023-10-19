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

import { ImageCollectionComponent } from '@/components/ImageCollection';
import {
	App,
	Attr,
	collectFieldData,
	create,
	createField,
	createGenericModal,
	createSelect,
	CSS,
	EventName,
	FieldTag,
	html,
	listen,
	uniqueId,
} from '@/core';
import { GalleryBlockData } from '@/types';
import { BaseBlock } from './BaseBlock';

export class GalleryBlock extends BaseBlock<GalleryBlockData> {
	/**
	 * Sanitizer settings.
	 *
	 * @static
	 */
	static get sanitize() {
		return {
			layout: false,
			columnWidth: false,
			rowHeight: false,
			gap: false,
		};
	}

	/**
	 * Toolbox settings.
	 */
	static get toolbox() {
		return {
			title: App.text('galleryBlockTitle'),
			icon: '<i class="bi bi-columns"></i>',
		};
	}

	/**
	 * Prepare block data.
	 *
	 * @param data
	 * @param data.files
	 * @param data.layout
	 * @param data.columnWidth
	 * @param data.rowHeight
	 * @param data.gap
	 * @param data.cleanBottom
	 * @return the gallery block data
	 */
	protected prepareData(data: GalleryBlockData): GalleryBlockData {
		return {
			files: data.files || [],
			layout: data.layout || 'columns',
			columnWidth: data.columnWidth || '250px',
			rowHeight: data.rowHeight || '10rem',
			gap: data.gap || '5px',
			cleanBottom: data.cleanBottom ?? false,
		};
	}

	/**
	 * Render the main block element.
	 *
	 * @return the rendered block
	 */
	render(): HTMLElement {
		this.wrapper.classList.add(CSS.flex, CSS.flexColumn, CSS.flexGap);

		create(
			'span',
			[CSS.textMuted, CSS.userSelectNone],
			{},
			this.wrapper,
			html`
				<am-icon-text
					${Attr.icon}="columns"
					${Attr.text}="${GalleryBlock.toolbox.title}"
				></am-icon-text>
			`
		);

		const group = create(
			'div',
			[CSS.imageCollectionGroup],
			{},
			this.wrapper
		);

		const layoutButton = create(
			'button',
			[CSS.button],
			{},
			group,
			App.text('galleryBlockLayout')
		);

		this.api.listeners.on(
			layoutButton,
			'click',
			this.renderModal.bind(this)
		);

		const collection = create(
			ImageCollectionComponent.TAG_NAME,
			[],
			{},
			group
		) as ImageCollectionComponent;

		setTimeout(() => {
			collection.images = this.data.files;

			this.api.listeners.on(collection, 'change', () => {
				this.data.files = collection.images;
				this.blockAPI.dispatchChange();
			});
		}, 0);

		return this.wrapper;
	}

	/**
	 * Render a new layout settings modal.
	 */
	private renderModal(): void {
		const { modal, body } = createGenericModal(
			App.text('galleryBlockLayout')
		);

		setTimeout(() => {
			this.renderLayoutSettings(body);
			modal.open();
		}, 0);

		listen(modal, 'change', () => {
			this.data = {
				...this.data,
				cleanBottom: false, // Always set false, since false toggles are ignored by collectFieldData()
				...collectFieldData(modal),
			};
		});

		listen(modal, EventName.modalClose, () => {
			this.blockAPI.dispatchChange();
		});
	}

	/**
	 * Render the settings form.
	 *
	 * @param body
	 */
	private renderLayoutSettings(body: HTMLElement): void {
		body.innerHTML = '';

		const layout = createSelect(
			[
				{
					text: App.text('galleryBlockLayoutColumns'),
					value: 'columns',
				},
				{
					text: App.text('galleryBlockLayoutRows'),
					value: 'rows',
				},
			],
			this.data.layout,
			body,
			'layout'
		);

		createField(FieldTag.numberUnit, body, {
			name: 'gap',
			value: this.data.gap,
			key: uniqueId(),
			label: App.text('galleryBlockLayoutGap'),
		});

		if (this.data.layout == 'rows') {
			createField(FieldTag.numberUnit, body, {
				name: 'rowHeight',
				value: this.data.rowHeight,
				key: uniqueId(),
				label: App.text('galleryBlockLayoutRowHeight'),
			});
		} else {
			createField(FieldTag.numberUnit, body, {
				name: 'columnWidth',
				value: this.data.columnWidth,
				key: uniqueId(),
				label: App.text('galleryBlockLayoutColumnWidth'),
			});

			createField(FieldTag.toggle, body, {
				name: 'cleanBottom',
				value: this.data.cleanBottom,
				key: uniqueId(),
				label: App.text('galleryBlockLayoutCleanBottom'),
			});
		}

		const layoutListener = listen(layout, 'change', () => {
			layoutListener.remove();
			this.data.layout = layout.value as 'columns' | 'rows';
			this.renderLayoutSettings(body);
		});
	}

	/**
	 * Return the section block data.
	 *
	 * @return the saved data
	 */
	save(): GalleryBlockData {
		return this.data;
	}
}
