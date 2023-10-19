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
import { SliderBlockBreakpoints, SliderBlockData } from '@/types';
import { BaseBlock } from './BaseBlock';

/**
 * Slider transition effects.
 *
 * @see {@link docs https://swiperjs.com/swiper-api#param-effect}
 */
export const sliderEffects = ['slide', 'fade', 'cube', 'flip'] as const;

const breakpointsToString = (breakpoints: SliderBlockBreakpoints): string => {
	return Object.keys(breakpoints).reduce((out: string, minWidth: string) => {
		return `${out} ${minWidth}:${breakpoints[minWidth].slidesPerView}`.trim();
	}, '');
};

const stringToBreakpoints = (
	breakpointsString: string
): SliderBlockBreakpoints => {
	const breakpoints: SliderBlockBreakpoints = {};

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
export class SliderBlock extends BaseBlock<SliderBlockData> {
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
			title: App.text('sliderBlockTitle'),
			icon: '<i class="bi bi-collection-play"></i>',
		};
	}

	/**
	 * Prepare block data.
	 *
	 * @param data
	 * @param data.files
	 * @param data.spaceBetween
	 * @param data.slidesPerView
	 * @param data.loop
	 * @param data.autoplay
	 * @param data.effect
	 * @param data.breakpoints
	 * @return the slider block data
	 */
	protected prepareData(data: SliderBlockData): SliderBlockData {
		return {
			files: data.files || [],
			spaceBetween: data.spaceBetween || 30,
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
					${Attr.text}="${SliderBlock.toolbox.title}"
				></am-icon-text>
			`
		);

		const group = create(
			'div',
			[CSS.imageCollectionGroup],
			{},
			this.wrapper
		);

		const settingsButton = create(
			'button',
			[CSS.button],
			{},
			group,
			App.text('sliderBlockSettings')
		);

		this.api.listeners.on(
			settingsButton,
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
			App.text('sliderBlockSettings')
		);

		setTimeout(() => {
			this.renderSliderSettings(body);
			modal.open();
		}, 0);

		listen(modal, 'change', () => {
			const data = collectFieldData(modal);

			this.data = {
				files: this.data.files,
				spaceBetween: data.spaceBetween,
				slidesPerView: data.slidesPerView,
				loop: data.loop ?? false,
				autoplay: data.autoplay ?? false,
				effect: data.effect,
				breakpoints: stringToBreakpoints(data.breakpointsString),
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
	private renderSliderSettings(body: HTMLElement): void {
		body.innerHTML = '';

		const effect = create(
			'div',
			[CSS.field],
			{},
			body,
			html`
				<label class="${CSS.fieldLabel}">
					${App.text('sliderBlockEffect')}
				</label>
			`
		);

		createSelect(
			sliderEffects.map((effect) => ({ value: effect })),
			this.data.effect,
			effect,
			'effect'
		);

		createField(FieldTag.toggle, body, {
			name: 'loop',
			value: this.data.loop,
			key: uniqueId(),
			label: App.text('sliderBlockLoop'),
		});

		createField(FieldTag.toggle, body, {
			name: 'autoplay',
			value: this.data.autoplay,
			key: uniqueId(),
			label: App.text('sliderBlockAutoplay'),
		});

		const group = create('div', [CSS.grid, CSS.gridAuto], {}, body);

		createField(FieldTag.number, group, {
			name: 'slidesPerView',
			value: this.data.slidesPerView,
			key: uniqueId(),
			label: App.text('sliderBlockSlidesPerView'),
		});

		createField(FieldTag.number, group, {
			name: 'spaceBetween',
			value: this.data.spaceBetween,
			key: uniqueId(),
			label: App.text('sliderBlockSpaceBetween'),
		});

		create(
			'div',
			[CSS.field],
			{},
			body,
			html`
				<label class="${CSS.fieldLabel}">
					${App.text('sliderBlockBreakpoints')} &mdash;
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

		create('p', [], {}, body, App.text('sliderBlockBreakpointsHelp'));
	}

	/**
	 * Return the section block data.
	 *
	 * @return the saved data
	 */
	save(): SliderBlockData {
		return this.data;
	}
}
