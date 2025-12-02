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

import { ComponentImplementationConstructor } from './types';
import '../vendor/katex.scss';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/autoplay';
import 'swiper/css/effect-fade';
import 'swiper/css/effect-flip';
import 'photoswipe/style.css';
import 'photoswipe-dynamic-caption-plugin/photoswipe-dynamic-caption-plugin.css';
import './styles/index.less';

const components = [
	{
		tag: 'am-gallery',
		importer: async () => await import('@/blocks/components/Gallery'),
	},
	{
		tag: 'am-image-slideshow',
		importer: async () =>
			await import('@/blocks/components/ImageSlideshow'),
	},
	{
		tag: 'am-img-loader',
		importer: async () => await import('@/blocks/components/ImgLoader'),
	},
	{
		tag: 'am-inline-tex',
		importer: async () => await import('@/blocks/components/InlineTex'),
	},
	{
		tag: 'am-mail',
		importer: async () => await import('@/blocks/components/Mail'),
	},
	{
		tag: 'am-table-of-contents',
		importer: async () =>
			await import('@/blocks/components/TableOfContents'),
	},
	{
		tag: 'am-tex',
		importer: async () => await import('@/blocks/components/Tex'),
	},
] as const;

for (const { tag, importer } of components) {
	customElements.define(
		tag,
		class extends HTMLElement {
			constructor() {
				super();
			}

			async connectedCallback() {
				const component = (await importer())
					.default as unknown as ComponentImplementationConstructor;

				new component(this);
			}
		}
	);
}
