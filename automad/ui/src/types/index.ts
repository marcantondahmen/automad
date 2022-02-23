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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

export * from './form';
export * from './page';

export interface KeyValueMap {
	[key: string | number]: any;
}

export interface AutocompleteItem {
	element: HTMLElement;
	value: string;
	item: KeyValueMap;
}

export interface File {
	basename: string;
	extension: string;
	mtime: string;
	size: string;
	path: string;
	url: string;
	caption: string;
	thumbnail?: string;
	width?: number;
	height?: number;
}

export interface NavTreePageData {
	url: string;
	title: string;
	path: string;
	parent: string;
	private: boolean;
}

export interface NavTreeItem {
	wrapper: HTMLElement;
	summary: HTMLElement;
	children: HTMLElement;
	page: NavTreePageData;
}

export interface Partials {
	[key: string]: string;
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
	version?: string;
}

export interface ThemeCollection {
	[key: string]: Theme;
}

export interface UIState {
	sidebarScroll: number;
	documentScroll?: number;
	focusedId?: string;
	focusedCursorPosition?: number;
}
