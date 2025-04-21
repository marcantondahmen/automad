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

import { create, debounce } from '@/common';
import { GalleryData, MasonryItem } from '@/blocks/types';
import PhotoSwipe from 'photoswipe';
// @ts-ignore
import PhotoSwipeLightbox from 'photoswipe/lightbox';
// @ts-ignore
import PhotoSwipeDynamicCaption from 'photoswipe-dynamic-caption-plugin';
// @ts-ignore
import ObjectPosition from '@vovayatsyuk/photoswipe-object-position';
import 'photoswipe/style.css';
import 'photoswipe-dynamic-caption-plugin/photoswipe-dynamic-caption-plugin.css';
import arrowPrevSVG from '@/blocks/svg/arrowPrev.svg';
import arrowNextSVG from '@/blocks/svg/arrowNext.svg';
import closeSVG from '@/blocks/svg/close.svg';
import zoomSVG from '@/blocks/svg/zoom.svg';

const renderThumb = (imgSet: GalleryData['imageSets'][number]): string => {
	const { thumb, caption } = imgSet;

	return `
		<am-img-loader
			image="${thumb.image}"
			preload="${thumb.preload}"
			width="${thumb.width}"
			height="${thumb.height}"
		></am-img-loader>
		${caption ? `<span class="pswp-caption-content">${caption}</span>` : ''}
	`;
};

/**
 * A gallery component for column or row based layouts.
 *
 * @see {@link docs https://photoswipe.com}
 * @see {@link github https://github.com/dimsemenov/photoswipe}
 * @see {@link captions https://github.com/dimsemenov/photoswipe-dynamic-caption-plugin}
 */
class GalleryComponent extends HTMLElement {
	/**
	 * The tag name.
	 *
	 * @static
	 */
	static TAG_NAME = 'am-gallery';

	/**
	 * The gallery settings and files.
	 */
	private data: GalleryData;

	/**
	 * The class constructor.
	 */
	constructor() {
		super();
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.data = JSON.parse(
			decodeURIComponent(this.getAttribute('data') ?? '')
		) as GalleryData;

		this.removeAttribute('data');

		const layouts = {
			columns: this.renderColumns,
			grid: this.renderGrid,
			rows: this.renderRows,
		};

		const render = (
			layouts[this.data.settings.layout] ?? this.renderColumns
		).bind(this);

		render();
	}

	/**
	 * Render the grid layout.
	 */
	private renderGrid(): void {
		const gallery = create(
			'div',
			['am-gallery-grid'],
			{
				style: `
					--am-gallery-grid-item-aspect: ${this.data.settings.columnWidthPx / this.data.settings.rowHeightPx};
					--am-gallery-grid-item-width: ${this.data.settings.columnWidthPx}px;
					--am-gallery-gap: ${this.data.settings.gapPx}px;
				`,
			},
			this
		);

		this.data.imageSets.forEach((imgSet) => {
			create(
				'a',
				['am-gallery-grid-item'],
				{
					href: imgSet.large.image,
					target: '_blank',
					'data-pswp-width': imgSet.large.width,
					'data-pswp-height': imgSet.large.height,
					style: `--aspect: ${imgSet.thumb.width / imgSet.thumb.height}`,
				},
				gallery,
				`<div class="am-gallery-img-small">${renderThumb(imgSet)}</div>`
			);
		});
	}

	/**
	 * Render a column based masonry layout.
	 */
	private renderColumns(): void {
		const settings = this.data.settings;
		const masonryRowHeight = 10;

		const gallery = create(
			'div',
			['am-gallery-masonry'],
			{
				style: `
					--am-gallery-item-width: ${settings.columnWidthPx}px;
					--am-gallery-auto-rows: ${masonryRowHeight}px; 	
					--am-gallery-gap: ${settings.gapPx}px;
				`,
			},
			this
		);

		const items: MasonryItem[] = [];

		this.data.imageSets.forEach((imgSet) => {
			const element = create(
				'div',
				['am-gallery-masonry-item'],
				{},
				gallery
			);

			create(
				'a',
				['am-gallery-img-small'],
				{
					href: imgSet.large.image,
					target: '_blank',
					'data-pswp-width': imgSet.large.width,
					'data-pswp-height': imgSet.large.height,
					'data-cropped': 'true',
				},
				element,
				renderThumb(imgSet)
			);

			items.push({
				element,
				rowSpan: 0,
				height: element.getBoundingClientRect().height,
				thumbHeight: imgSet.thumb.height,
			});
		});

		const updateItems = (items: MasonryItem[]) => {
			const masonryWidth = this.getBoundingClientRect().width;
			const width = settings.columnWidthPx - settings.gapPx;
			const nCols = Math.floor(masonryWidth / width) || 1;
			const factor = (nCols * width) / masonryWidth;

			items.forEach((item) => {
				item.element.removeAttribute('style');

				const rowSpan = Math.round(
					item.thumbHeight / (masonryRowHeight * factor)
				);

				item.rowSpan = rowSpan;
				item.element.setAttribute(
					'style',
					`--am-gallery-masonry-rows: ${item.rowSpan};`
				);

				item.height = item.element.getBoundingClientRect().height;
			});

			if (settings.cleanBottom) {
				const columns: { [key: number]: MasonryItem[] } = {};
				const nRows = Math.ceil(
					this.getBoundingClientRect().height / masonryRowHeight
				);

				let columnNumber = 0;

				// Create a columns object with the x coordinate as key.
				// All items sharing the same x value get stored in the same "column".
				items.forEach((item) => {
					// Add 1000 to index to always be positive with negative margins.
					// This is needed to keep the object sorted and get the right column number.
					const x =
						Math.ceil(item.element.getBoundingClientRect().x) +
						1000;

					columns[x] = columns[x] ?? [];
					columns[x].push(item);
				});

				for (const [x, column] of Object.entries(columns)) {
					let columnRows = 0;
					let rowsFromTop = 1;
					let rest = nRows;
					let columnHeight = 0;

					columnNumber++;

					// Set column start for each element and
					// collect number of rows used of the column.
					column.forEach((item) => {
						item.element.style.gridColumnStart = `${columnNumber}`;
						columnRows += item.rowSpan;
						columnHeight += item.height;
					});

					// Calculate the diff of the used rows with the full number
					// of rows spanned by the container.
					const diff = nRows - columnRows;

					// Distribute the diffRows to each item in a column.
					// The last item simply get the rest, in case there are left
					// over rows due to rounding.
					column.forEach(({ element, rowSpan, height }, index) => {
						const addSpan =
							rowSpan +
							Math.floor((height / columnHeight) * diff);

						if (index == column.length - 1) {
							element.style.gridRowStart = `${rowsFromTop}`;
							element.style.gridRowEnd = 'span ' + rest;
						} else {
							element.style.gridRowStart = `${rowsFromTop}`;
							element.style.gridRowEnd = 'span ' + addSpan;
							rest -= addSpan;
							rowsFromTop += addSpan;
						}
					});
				}
			}
		};

		updateItems(items);

		window.addEventListener(
			'resize',
			debounce(() => {
				updateItems(items);
			}, 100)
		);
	}

	/**
	 * Render a row based flex layout.
	 */
	private renderRows(): void {
		const gallery = create(
			'div',
			['am-gallery-flex'],
			{
				style: `
					--am-gallery-flex-item-height: ${this.data.settings.rowHeightPx}px;
					--am-gallery-gap: ${this.data.settings.gapPx}px;
				`,
			},
			this
		);

		this.data.imageSets.forEach((imgSet) => {
			create(
				'a',
				['am-gallery-flex-item', 'am-gallery-img-small'],
				{
					href: imgSet.large.image,
					target: '_blank',
					'data-pswp-width': imgSet.large.width,
					'data-pswp-height': imgSet.large.height,
				},
				gallery,
				renderThumb(imgSet)
			);
		});
	}
}

customElements.define(GalleryComponent.TAG_NAME, GalleryComponent);

/**
 * Initialize all galleries at once in order to allow for merged lighbox image sets.
 */
const initLightbox = (): void => {
	const lightbox = new PhotoSwipeLightbox({
		gallery: 'body',
		children: `${GalleryComponent.TAG_NAME} a`,
		showHideAnimationType: 'zoom',
		showAnimationDuration: 300,
		hideAnimationDuration: 300,
		pswpModule: PhotoSwipe,
		mainClass: 'am-pswp',
		bgOpacity: 1,
		arrowPrevSVG,
		arrowNextSVG,
		closeSVG,
		zoomSVG,
	});

	new PhotoSwipeDynamicCaption(lightbox, {
		type: 'auto',
	});

	new ObjectPosition(lightbox);

	lightbox.init();
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initLightbox);
} else {
	initLightbox();
}
