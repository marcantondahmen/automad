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
import {
	GalleryData,
	GalleryRow,
	ImageSetData,
	MasonryItem,
} from '@/blocks/types';
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
				`<div class="am-gallery-img-small">${this.renderThumb(imgSet)}</div>`
			);
		});
	}

	/**
	 * Calculate the optimal row height in pixels for the underlaying
	 * CSS grid. Ideally the height is 1px to be as precise as possible.
	 * When the grid is getting to long, that number will increase.
	 *
	 * @return number
	 */
	private calculateMasonryRowHeight(): number {
		const maxRows = 10000;
		const masonryWidth = this.getBoundingClientRect().width;
		const { settings, imageSets } = this.data;
		const colWidth = settings.columnWidthPx;
		const gap = settings.gapPx;
		const nCols = Math.ceil(masonryWidth / colWidth);
		const estimatedHeight = (imageSets.length / nCols) * (colWidth + gap);

		return Math.ceil(estimatedHeight / maxRows);
	}

	/**
	 * Render a column based masonry layout.
	 */
	private renderColumns(): void {
		const settings = this.data.settings;
		const gallery = create(
			'div',
			['am-gallery-masonry'],
			{ hidden: 'true' },
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
				this.renderThumb(imgSet)
			);

			items.push({
				element,
				rowSpan: 0,
				height: element.getBoundingClientRect().height,
				thumbHeight: Math.round(
					imgSet.thumb.height / this.data.settings.pixelDensity
				),
			});
		});

		const updateItems = (items: MasonryItem[]) => {
			const masonryRowHeight = this.calculateMasonryRowHeight();
			const masonryWidth = this.getBoundingClientRect().width;
			const colWidth = settings.columnWidthPx;
			const gap = settings.gapPx;

			gallery.setAttribute(
				'style',
				`
					--am-gallery-item-width: ${colWidth}px;
					--am-gallery-auto-rows: ${masonryRowHeight}px; 	
					--am-gallery-gap: ${gap}px;
				`
			);

			const nCols = window
				.getComputedStyle(gallery)
				.getPropertyValue('grid-template-columns')
				.split(' ').length;

			const masonryWidthNoGap = masonryWidth - (nCols - 1) * gap;
			const width = masonryWidthNoGap / nCols;
			const factor = width / colWidth;

			items.forEach((item) => {
				item.element.removeAttribute('style');

				item.rowSpan = Math.round(
					(item.thumbHeight * factor + gap) / masonryRowHeight
				);

				item.element.setAttribute(
					'style',
					`--am-gallery-masonry-rows: ${item.rowSpan};`
				);

				item.height = item.element.getBoundingClientRect().height;
			});

			if (settings.fillRectangle) {
				const columns: { [key: number]: MasonryItem[] } = {};
				const nRows = Math.round(
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

			gallery.removeAttribute('hidden');
		};

		const debounced = debounce(updateItems.bind(this, items), 50);

		setTimeout(debounced, 0);
		window.addEventListener('resize', debounced);
		window.addEventListener('load', debounced);
	}

	/**
	 * Render a row based flex layout.
	 */
	private renderRows(): void {
		const gap = this.data.settings.gapPx;
		const gallery = create(
			'div',
			[
				'am-gallery-flex',
				...(gap > 1 ? ['am-gallery-flex--contain'] : []),
			],
			{ style: `--am-gallery-gap: ${gap}px;` },
			this
		);

		const calcWidth = (width: number): number => {
			return gap + width / this.data.settings.pixelDensity;
		};

		const createRow = (
			container: HTMLElement,
			scale: number
		): HTMLElement => {
			return create(
				'div',
				['am-gallery-flex-row'],
				{
					style: `--am-gallery-flex-row-height: ${Math.round(this.data.settings.rowHeightPx * scale)}px`,
				},
				container
			);
		};

		const createImageSet = (
			is: ImageSetData,
			container: HTMLElement
		): void => {
			create(
				'a',
				['am-gallery-img-small'],
				{
					href: is.large.image,
					target: '_blank',
					'data-pswp-width': is.large.width,
					'data-pswp-height': is.large.height,
				},
				container,
				this.renderThumb(is)
			);
		};

		const updateItems = () => {
			gallery.innerHTML = '';

			const containerWidth = gallery.getBoundingClientRect().width;
			let currentRow: ImageSetData[] = [];
			let accWidth = -gap;

			const calcScale = (
				rowWidth: number,
				numberOfItems: number
			): number => {
				const gaps = (numberOfItems - 1) * gap;

				return (containerWidth - gaps) / (rowWidth - gaps);
			};

			if (this.data.settings.fillRectangle) {
				const rowsReversed: GalleryRow[] = [];

				[...this.data.imageSets].reverse().forEach((imgSet, index) => {
					const { thumb } = imgSet;

					currentRow.push(imgSet);
					accWidth += calcWidth(thumb.width);

					if (
						accWidth > containerWidth ||
						index == this.data.imageSets.length - 1
					) {
						rowsReversed.push({
							width: accWidth,
							imageSets: currentRow.reverse(),
						});

						accWidth = -gap;
						currentRow = [];
					}
				});

				const rows: GalleryRow[] = rowsReversed.reverse();

				let indexOfLastRowRemovedFrom = 1;

				while (
					rows[0].width * 2 < containerWidth &&
					indexOfLastRowRemovedFrom < rows.length &&
					rows[indexOfLastRowRemovedFrom].width * 2 > rows[0].width
				) {
					const moved =
						rows[indexOfLastRowRemovedFrom].imageSets.shift();

					rows[0].imageSets.push(moved);
					rows[0].width += calcWidth(moved.thumb.width);

					rows[indexOfLastRowRemovedFrom].width -= calcWidth(
						moved.thumb.width
					);

					indexOfLastRowRemovedFrom++;
				}

				rows.forEach((row) => {
					const scale = calcScale(row.width, row.imageSets.length);
					const rowContainer = createRow(gallery, scale);

					row.imageSets.forEach((is) => {
						createImageSet(is, rowContainer);
					});
				});
			} else {
				this.data.imageSets.forEach((imgSet, index) => {
					const { thumb } = imgSet;

					currentRow.push(imgSet);
					accWidth += calcWidth(thumb.width);

					if (
						accWidth > containerWidth ||
						index == this.data.imageSets.length - 1
					) {
						const scale = Math.min(
							calcScale(accWidth, currentRow.length),
							1
						);
						const row = createRow(gallery, scale);

						currentRow.forEach((is) => {
							createImageSet(is, row);
						});

						accWidth = -gap;
						currentRow = [];
					}
				});
			}
		};

		updateItems();

		const debounced = debounce(updateItems.bind(this), 50);

		window.addEventListener('resize', debounced);
		window.addEventListener('load', debounced);
	}

	/**
	 * Render a thumbnail of an image.
	 *
	 * @param imgSet
	 */
	private renderThumb(imgSet: ImageSetData): string {
		const { thumb, caption } = imgSet;

		return `
		<am-img-loader
			image="${thumb.image}"
			preload="${thumb.preload}"
			width="${Math.round(thumb.width / this.data.settings.pixelDensity)}"
			height="${Math.round(thumb.height / this.data.settings.pixelDensity)}"
		></am-img-loader>
		${caption ? `<span class="pswp-caption-content">${caption}</span>` : ''}
	`;
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
