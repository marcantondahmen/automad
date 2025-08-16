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

import { ImageCollectionComponent } from '@/admin/components/ImageCollection';
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
} from '@/admin/core';
import { GalleryBlockData, GalleryLayout } from '@/admin/types';
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
			columnWidthPx: false,
			rowHeightPx: false,
			gapPx: false,
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
	 * @param data.columnWidthPx
	 * @param data.rowHeightPx
	 * @param data.gapPx
	 * @param data.fillRectangle
	 * @return the gallery block data
	 */
	protected prepareData(data: GalleryBlockData): GalleryBlockData {
		return {
			files: data.files || [],
			layout: data.layout || 'columns',
			columnWidthPx: data.columnWidthPx || 250,
			rowHeightPx: data.rowHeightPx || 250,
			gapPx: data.gapPx || 5,
			fillRectangle: data.fillRectangle ?? false,
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

		if (!this.readOnly) {
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
		}

		const collection = create(
			ImageCollectionComponent.TAG_NAME,
			[],
			this.readOnly ? { disabled: '' } : {},
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
				fillRectangle: false, // Always set false, since false toggles are ignored by collectFieldData()
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
					text: App.text('galleryBlockLayoutGrid'),
					value: 'grid',
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

		const settings = create('div', [CSS.grid, CSS.gridAuto], {}, body);

		createField(
			FieldTag.number,
			settings,
			{
				name: 'gapPx',
				value: this.data.gapPx,
				key: uniqueId(),
				label: `${App.text('galleryBlockLayoutGap')} (Pixel)`,
			},
			[],
			{ required: '' }
		);

		if (['columns', 'rows'].includes(this.data.layout)) {
			createField(FieldTag.toggle, settings, {
				name: 'fillRectangle',
				value: this.data.fillRectangle,
				key: uniqueId(),
				label: App.text('galleryBlockLayoutFillRectangle'),
			});
		}

		const dimensions = create('div', [CSS.grid, CSS.gridAuto], {}, body);

		if (['rows', 'grid'].includes(this.data.layout)) {
			createField(
				FieldTag.number,
				dimensions,
				{
					name: 'rowHeightPx',
					value: this.data.rowHeightPx,
					key: uniqueId(),
					label: `${App.text('galleryBlockLayoutRowHeight')} (Pixel)`,
				},
				[],
				{ required: '' }
			);
		}

		if (['columns', 'grid'].includes(this.data.layout)) {
			createField(
				FieldTag.number,
				dimensions,
				{
					name: 'columnWidthPx',
					value: this.data.columnWidthPx,
					key: uniqueId(),
					label: `${App.text(
						'galleryBlockLayoutColumnWidth'
					)} (Pixel)`,
				},
				[],
				{ required: '' }
			);
		}

		const layoutListener = listen(layout, 'change', () => {
			layoutListener.remove();
			this.data.layout = layout.value as GalleryLayout;
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
