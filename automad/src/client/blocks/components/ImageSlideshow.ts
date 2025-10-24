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

import { SliderData } from '@/blocks/types';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/autoplay';
import 'swiper/css/effect-fade';
import 'swiper/css/effect-flip';
import { create } from '@/common';

/**
 * A slider component based on Swiper.js.
 *
 * @see {@link docs https://swiperjs.com}
 * @see {@link github https://github.com/nolimits4web/swiper}
 */
export default class Slider {
	/**
	 * The gallery settings and files.
	 */
	private data: SliderData;

	/**
	 * The class constructor.
	 */
	constructor(element: HTMLElement) {
		this.data = JSON.parse(
			decodeURIComponent(element.getAttribute('data') ?? '')
		) as SliderData;

		element.removeAttribute('data');
		this.render(element);
	}

	/**
	 * Render the slider.
	 *
	 * @param element
	 */
	private async render(element: HTMLElement): Promise<void> {
		const Swiper = (await import('swiper')).default;
		const { Autoplay, EffectFade, EffectFlip, Navigation, Pagination } =
			await import('swiper/modules');

		const swiperContainer = create('div', ['swiper'], {}, element);
		const wrapper = create('div', ['swiper-wrapper'], {}, swiperContainer);
		const { settings, imageSets } = this.data;

		create('div', ['swiper-pagination'], {}, swiperContainer);
		create('div', ['swiper-button-prev'], {}, swiperContainer);
		create('div', ['swiper-button-next'], {}, swiperContainer);

		imageSets.forEach(({ imageSet, caption }) => {
			const captionHtml = caption
				? `<div class="swiper-caption">${caption}</div>`
				: '';

			create(
				'div',
				['swiper-slide'],
				{},
				wrapper,
				`
					<am-img-loader 
						image="${imageSet.image}" 
						preload="${imageSet.preload}" 
						width="${imageSet.width}" 
						height="${imageSet.height}"
					></am-img-loader>
					${captionHtml}
				`
			);
		});

		new Swiper(swiperContainer, {
			modules: [Autoplay, EffectFade, EffectFlip, Navigation, Pagination],
			pagination: {
				el: '.swiper-pagination',
				clickable: true,
			},
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			grabCursor: true,
			speed: 300,
			loop: settings.loop ?? true,
			autoplay: settings.autoplay ?? false,
			effect: settings.effect ?? 'slide',
			slidesPerView: settings.slidesPerView || 1,
			spaceBetween: settings.gapPx ?? 0,
			breakpoints: settings.breakpoints ?? {},
			breakpointsBase: 'container',
		});
	}
}
