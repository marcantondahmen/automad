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
	createSelectField,
	CSS,
	EventName,
	FieldTag,
	html,
	uniqueId,
} from '@/admin/core';
import {
	ImageSlideshowBreakpoints,
	ImageSlideshowBlockData,
} from '@/admin/types';
import { BaseBlock } from './BaseBlock';

/**
 * Slider transition effects.
 *
 * @see {@link docs https://swiperjs.com/swiper-api#param-effect}
 */
export const sliderEffects = ['slide', 'fade', 'flip'] as const;

const breakpointsToString = (
	breakpoints: ImageSlideshowBreakpoints
): string => {
	return Object.keys(breakpoints).reduce((out: string, minWidth: string) => {
		return `${out} ${minWidth}:${breakpoints[minWidth].slidesPerView}`.trim();
	}, '');
};

const stringToBreakpoints = (
	breakpointsString: string
): ImageSlideshowBreakpoints => {
	const breakpoints: ImageSlideshowBreakpoints = {};

	breakpointsString.split(' ').forEach((pair: string) => {
		const [minWidth, slidesPerView] = pair.split(':');

		if (minWidth && slidesPerView) {
			breakpoints[minWidth] = {
				slidesPerView: parseInt(slidesPerView),
			};
		}
	});

	return breakpoints;
};

/**
 * A slider block base on Swiper.js
 *
 * @see {@link swiper https://swiperjs.com}
 * @see {@link docs https://swiperjs.com/swiper-api#parameters}
 * @see {@link github https://github.com/nolimits4web/swiper}
 */
export class ImageSlideshowBlock extends BaseBlock<ImageSlideshowBlockData> {
	/**
	 * Sanitizer settings.
	 *
	 * @static
	 */
	static get sanitize() {
		return {
			effect: false,
		};
	}

	/**
	 * Toolbox settings.
	 */
	static get toolbox() {
		return {
			title: App.text('imageSlideshowBlockTitle'),
			icon: '<i class="bi bi-collection-play"></i>',
		};
	}

	/**
	 * Prepare block data.
	 *
	 * @param data
	 * @param data.files
	 * @param data.imageWidthPx
	 * @param data.imageHeightPx
	 * @param data.gapPx
	 * @param data.slidesPerView
	 * @param data.loop
	 * @param data.autoplay
	 * @param data.effect
	 * @param data.breakpoints
	 * @return the slider block data
	 */
	protected prepareData(
		data: ImageSlideshowBlockData
	): ImageSlideshowBlockData {
		return {
			files: data.files || [],
			imageWidthPx: data.imageWidthPx || 1200,
			imageHeightPx: data.imageHeightPx || 780,
			gapPx: data.gapPx || 30,
			slidesPerView: data.slidesPerView || 1,
			loop: data.loop ?? true,
			autoplay: data.autoplay ?? false,
			effect: data.effect ?? 'slide',
			breakpoints: data.breakpoints ?? {
				600: {
					slidesPerView: 2,
				},
				900: {
					slidesPerView: 3,
				},
			},
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
					${Attr.icon}="collection-play"
					${Attr.text}="${ImageSlideshowBlock.toolbox.title}"
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
			const settingsButton = create(
				'button',
				[CSS.button],
				{},
				group,
				App.text('imageSlideshowBlockSettings')
			);

			this.listen(settingsButton, 'click', this.renderModal.bind(this));
		}

		const collection = create(
			ImageCollectionComponent.TAG_NAME,
			[],
			this.readOnly ? { disabled: '' } : {},
			group
		) as ImageCollectionComponent;

		setTimeout(() => {
			collection.images = this.data.files;

			if (!this.readOnly) {
				this.listen(collection, 'change', () => {
					this.data.files = collection.images;
					this.blockAPI.dispatchChange();
				});
			}
		}, 0);

		return this.wrapper;
	}

	/**
	 * Render a new layout settings modal.
	 */
	private renderModal(): void {
		const { modal, body } = createGenericModal(
			App.text('imageSlideshowBlockSettings')
		);

		setTimeout(() => {
			this.renderSliderSettings(body);
			modal.open();
		}, 0);

		this.listen(modal, 'change', () => {
			const data = collectFieldData(modal);

			this.data = {
				files: this.data.files,
				imageWidthPx: data.imageWidthPx,
				imageHeightPx: data.imageHeightPx,
				gapPx: data.gapPx,
				slidesPerView: data.slidesPerView,
				loop: data.loop ?? false,
				autoplay: data.autoplay ?? false,
				effect: data.effect,
				breakpoints: stringToBreakpoints(data.breakpointsString),
			};
		});

		this.listen(modal, EventName.modalClose, () => {
			this.blockAPI.dispatchChange();
		});
	}

	/**
	 * Render the settings form.
	 *
	 * @param body
	 */
	private renderSliderSettings(body: HTMLElement): void {
		body.innerHTML = '';

		createSelectField(
			App.text('imageSlideshowBlockEffect'),
			createSelect(
				sliderEffects.map((effect) => ({ value: effect })),
				this.data.effect,
				null,
				'effect'
			),
			body
		);

		createField(FieldTag.toggle, body, {
			name: 'loop',
			value: this.data.loop,
			key: uniqueId(),
			label: App.text('imageSlideshowBlockLoop'),
		});

		createField(FieldTag.toggle, body, {
			name: 'autoplay',
			value: this.data.autoplay,
			key: uniqueId(),
			label: App.text('imageSlideshowBlockAutoplay'),
		});

		const dimensions = create('div', [CSS.grid, CSS.gridAuto], {}, body);
		const layout = create('div', [CSS.grid, CSS.gridAuto], {}, body);

		createField(
			FieldTag.number,
			dimensions,
			{
				name: 'imageWidthPx',
				value: this.data.imageWidthPx,
				key: uniqueId(),
				label: `${App.text('imageSlideshowBlockImageWidth')} (Pixel)`,
			},
			[],
			{ required: '' }
		);

		createField(
			FieldTag.number,
			dimensions,
			{
				name: 'imageHeightPx',
				value: this.data.imageHeightPx,
				key: uniqueId(),
				label: `${App.text('imageSlideshowBlockImageHeight')} (Pixel)`,
			},
			[],
			{ required: '' }
		);

		createField(
			FieldTag.number,
			layout,
			{
				name: 'slidesPerView',
				value: this.data.slidesPerView,
				key: uniqueId(),
				label: App.text('imageSlideshowBlockSlidesPerView'),
			},
			[],
			{ required: '' }
		);

		createField(
			FieldTag.number,
			layout,
			{
				name: 'gapPx',
				value: this.data.gapPx,
				key: uniqueId(),
				label: `${App.text('imageSlideshowBlockSpaceBetween')} (Pixel)`,
			},
			[],
			{ required: '' }
		);

		create(
			'div',
			[CSS.field],
			{ [Attr.error]: App.text('imageSlideshowBlockBreakpointsError') },
			body,
			html`
				<label class="${CSS.fieldLabel}">
					${App.text('imageSlideshowBlockBreakpoints')} &mdash;
					<span class="${CSS.textMono}">px:slides px:slides ...</span>
				</label>
				<input
					class="${CSS.input} ${CSS.textMono}"
					type="text"
					value="${breakpointsToString(this.data.breakpoints)}"
					name="breakpointsString"
					placeholder="600:2 900:3"
					pattern="([1-9][0-9]+:[0-9]+( |$))*"
				/>
			`
		);

		create(
			'p',
			[],
			{},
			body,
			App.text('imageSlideshowBlockBreakpointsHelp')
		);
	}

	/**
	 * Return the section block data.
	 *
	 * @return the saved data
	 */
	save(): ImageSlideshowBlockData {
		return this.data;
	}
}
