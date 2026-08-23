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
 * Copyright (c) 2021-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

export * from '@/common/types';

import type { KeyValueMap } from '@/common';

declare global {
	const DEVELOPMENT: boolean;

	interface Event {
		path: string[];
	}

	interface ParentNode {
		closest: any;
	}

	interface Window {
		DEBUG: boolean;
	}
}

export type InputElement = HTMLInputElement | HTMLTextAreaElement;

export interface FocalPoint {
	x: number;
	y: number;
}

export interface Listener {
	remove: () => void;
}

export interface PackageDirectoryItems {
	pagelist: string[];
	filelist: string[];
	snippets: string[];
}

export interface PageMetaData {
	title: string;
	index: string;
	url: string;
	path: string;
	parentUrl: string;
	private: boolean;
	lastModified: string;
	publicationState: 'published' | 'draft';
}

export interface Pages {
	[key: string]: PageMetaData;
}

export type PublicationState = 'draft' | 'published';

export interface ThemeOptions {
	[key: string]: KeyValueMap;
}

export interface Theme {
	author: string;
	description: string;
	license: string;
	name: string;
	path: string;
	readme: string;
	templates: string[];
	tooltips: KeyValueMap;
	options: ThemeOptions;
	labels: KeyValueMap;
	version?: string;
}

export interface ThemeCollection {
	[key: string]: Theme;
}
