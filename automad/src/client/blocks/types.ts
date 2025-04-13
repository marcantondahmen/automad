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

import { GalleryBlockData, ImageSlideshowBlockData } from '@/admin/types';

export interface GalleryData {
	imageSets: {
		thumb: {
			image: string;
			width: number;
			height: number;
			preload: string;
		};
		large: {
			image: string;
			width: number;
			height: number;
		};
		caption: string;
	}[];
	settings: Omit<GalleryBlockData, 'files'>;
}

export interface MasonryItem {
	element: HTMLElement;
	rowSpan: number;
	height: number;
	thumbHeight: number;
}

export interface SliderData {
	imageSets: {
		imageSet: {
			image: string;
			width: number;
			height: number;
			preload: string;
		};
		caption: string;
	}[];
	settings: Omit<ImageSlideshowBlockData, 'files'>;
}
