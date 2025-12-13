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
// @ts-ignore
import PhotoSwipeLightbox from 'photoswipe/lightbox';
// @ts-ignore
import PhotoSwipeDynamicCaption from 'photoswipe-dynamic-caption-plugin';
// @ts-ignore
import ObjectPosition from '@vovayatsyuk/photoswipe-object-position';
import arrowPrevSVG from '@/blocks/svg/arrowPrev.svg';
import arrowNextSVG from '@/blocks/svg/arrowNext.svg';
import closeSVG from '@/blocks/svg/close.svg';

const cls = {
	pswpItem: 'pswp-item',
};

/**
 * A gallery component for column or row based layouts.
 *
 * @see {@link docs https://photoswipe.com}
 * @see {@link github https://github.com/dimsemenov/photoswipe}
 * @see {@link captions https://github.com/dimsemenov/photoswipe-dynamic-caption-plugin}
 */
export default class Gallery {
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
	 * The main parent element.
	 */
	element: HTMLElement;

	/**
	 * The class constructor.
	 */
	constructor(element: HTMLElement) {
		this.element = element;

		this.data = JSON.parse(
			decodeURIComponent(this.element.getAttribute('data') ?? '')
		) as GalleryData;

		this.element.removeAttribute('data');

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
			this.element
		);

		this.data.imageSets.forEach((imgSet) => {
			const element = create(
				'div',
				['am-gallery-grid-item', cls.pswpItem],
				{
					style: `--aspect: ${imgSet.thumb.width / imgSet.thumb.height}`,
				},
				gallery,
				this.renderCaption(imgSet)
			);

			create(
				'a',
				['am-gallery-img-small'],
				{
					href: imgSet.large.image,
					target: '_blank',
					'data-pswp-width': imgSet.large.width,
					'data-pswp-height': imgSet.large.height,
				},
				element,
				this.renderThumb(imgSet),
				true
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
		const masonryWidth = this.element.getBoundingClientRect().width;
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
		const { columnWidthPx, gapPx, fillRectangle } = this.data.settings;
		const { imageSets } = this.data;
		const gallery = create(
			'div',
			['am-gallery-masonry'],
			{ hidden: 'true' },
			this.element
		);

		const items: MasonryItem[] = [];

		const calcHeight = (thumb: {
			width: number;
			height: number;
		}): number => {
			return Math.round((thumb.height / thumb.width) * columnWidthPx);
		};

		imageSets.forEach((imgSet) => {
			const element = create(
				'div',
				['am-gallery-masonry-item', cls.pswpItem],
				{},
				gallery,
				this.renderCaption(imgSet)
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
				this.renderThumb(imgSet),
				true
			);

			items.push({
				element,
				rowSpan: 0,
				height: element.getBoundingClientRect().height,
				thumbHeight: calcHeight(imgSet.thumb),
			});
		});

		const updateItems = (items: MasonryItem[]) => {
			const masonryRowHeight = this.calculateMasonryRowHeight();
			const masonryWidth = this.element.getBoundingClientRect().width;

			gallery.setAttribute(
				'style',
				`
					--am-gallery-item-width: ${columnWidthPx}px;
					--am-gallery-auto-rows: ${masonryRowHeight}px; 	
					--am-gallery-gap: ${gapPx}px;
				`
			);

			const nCols = window
				.getComputedStyle(gallery)
				.getPropertyValue('grid-template-columns')
				.split(' ').length;

			const masonryWidthNoGap = masonryWidth - (nCols - 1) * gapPx;
			const width = masonryWidthNoGap / nCols;
			const factor = width / columnWidthPx;

			items.forEach((item) => {
				item.element.removeAttribute('style');

				item.rowSpan = Math.round(
					(item.thumbHeight * factor + gapPx) / masonryRowHeight
				);

				item.element.setAttribute(
					'style',
					`--am-gallery-masonry-rows: ${item.rowSpan};`
				);

				item.height = item.element.getBoundingClientRect().height;
			});

			if (fillRectangle) {
				const columns: { [key: number]: MasonryItem[] } = {};
				const nRows = Math.round(
					this.element.getBoundingClientRect().height /
						masonryRowHeight
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
		const { gapPx, rowHeightPx, fillRectangle } = this.data.settings;
		const { imageSets } = this.data;
		const gallery = create(
			'div',
			[
				'am-gallery-flex',
				...(fillRectangle ? ['am-gallery-flex--fill'] : []),
			],
			{ style: `--am-gallery-gap: ${gapPx}px;` },
			this.element
		);

		const calcWidth = (thumb: {
			width: number;
			height: number;
		}): number => {
			return (
				gapPx + Math.round((thumb.width / thumb.height) * rowHeightPx)
			);
		};

		const createRow = (
			container: HTMLElement,
			scale: number
		): HTMLElement => {
			return create(
				'div',
				['am-gallery-flex-row'],
				{
					style: `--am-gallery-flex-row-height: ${Math.round(rowHeightPx * scale)}px`,
				},
				container
			);
		};

		const createImageSet = (
			is: ImageSetData,
			container: HTMLElement
		): void => {
			const element = create(
				'div',
				[cls.pswpItem],
				{},
				container,
				this.renderCaption(is)
			);

			create(
				'a',
				['am-gallery-img-small'],
				{
					href: is.large.image,
					target: '_blank',
					'data-pswp-width': is.large.width,
					'data-pswp-height': is.large.height,
				},
				element,
				this.renderThumb(is),
				true
			);
		};

		const updateItems = () => {
			gallery.innerHTML = '';

			const containerWidth = gallery.getBoundingClientRect().width;
			let currentRow: ImageSetData[] = [];
			let accWidth = -gapPx;

			const calcScale = (
				rowWidth: number,
				numberOfItems: number
			): number => {
				const gaps = (numberOfItems - 1) * gapPx;

				return (containerWidth - gaps) / (rowWidth - gaps);
			};

			if (fillRectangle) {
				const rowsReversed: GalleryRow[] = [];

				[...imageSets].reverse().forEach((imgSet, index) => {
					const { thumb } = imgSet;

					currentRow.push(imgSet);
					accWidth += calcWidth(thumb);

					if (
						accWidth > containerWidth ||
						index == imageSets.length - 1
					) {
						rowsReversed.push({
							width: accWidth,
							imageSets: currentRow.reverse(),
						});

						accWidth = -gapPx;
						currentRow = [];
					}
				});

				const rows: GalleryRow[] = rowsReversed.reverse();

				let indexOfLastRowRemovedFrom = 1;

				while (
					rows[0].width * 2 < containerWidth &&
					indexOfLastRowRemovedFrom < rows.length &&
					rows[indexOfLastRowRemovedFrom].imageSets.length >
						rows[0].imageSets.length + 1
				) {
					const moved =
						rows[indexOfLastRowRemovedFrom].imageSets.shift();

					rows[0].imageSets.push(moved);
					rows[0].width += calcWidth(moved.thumb);

					rows[indexOfLastRowRemovedFrom].width -= calcWidth(
						moved.thumb
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
				imageSets.forEach((imgSet, index) => {
					const { thumb } = imgSet;

					currentRow.push(imgSet);
					accWidth += calcWidth(thumb);

					if (
						accWidth > containerWidth ||
						index == imageSets.length - 1
					) {
						const scale = Math.min(
							calcScale(accWidth, currentRow.length),
							1
						);
						const row = createRow(gallery, scale);

						currentRow.forEach((is) => {
							createImageSet(is, row);
						});

						accWidth = -gapPx;
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
	 * Render the caption.
	 *
	 * @param imgSet
	 */
	private renderCaption(imgSet: ImageSetData): string {
		return imgSet.caption
			? `<div class="pswp-caption-content">${imgSet.caption}</div>`
			: '';
	}

	/**
	 * Render a thumbnail of an image.
	 *
	 * @param imgSet
	 */
	private renderThumb(imgSet: ImageSetData): string {
		const { thumb } = imgSet;

		return `
			<am-img-loader
				image="${thumb.image}"
				preload="${thumb.preload}"
				width="${Math.round(thumb.width)}"
				height="${Math.round(thumb.height)}"
			></am-img-loader>
		`;
	}
}

/**
 * Initialize all galleries at once in order to allow for merged lighbox image sets.
 */
const initLightbox = async (): Promise<void> => {
	const lightbox = new PhotoSwipeLightbox({
		gallery: 'body',
		children: `${Gallery.TAG_NAME} .${cls.pswpItem}`,
		showHideAnimationType: 'zoom',
		showAnimationDuration: 300,
		hideAnimationDuration: 300,
		pswpModule: (await import('photoswipe')).default,
		mainClass: 'am-pswp',
		bgOpacity: 1,
		arrowPrevSVG,
		arrowNextSVG,
		closeSVG,
		zoom: false,
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
