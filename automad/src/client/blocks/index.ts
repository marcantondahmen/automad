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

import './styles/index.less';
import '../katex/index.scss';
import { ComponentImplementationConstructor } from './types';

const components = [
	{
		tag: 'am-gallery',
		importer: async () => await import('./components/Gallery'),
	},
	{
		tag: 'am-image-slideshow',
		importer: async () => await import('./components/ImageSlideshow'),
	},
	{
		tag: 'am-img-loader',
		importer: async () => await import('./components/ImgLoader'),
	},
	{
		tag: 'am-inline-tex',
		importer: async () => await import('./components/InlineTex'),
	},
	{
		tag: 'am-mail',
		importer: async () => await import('./components/Mail'),
	},
	{
		tag: 'am-table-of-contents',
		importer: async () => await import('./components/TableOfContents'),
	},
	{
		tag: 'am-tex',
		importer: async () => await import('./components/Tex'),
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
