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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

export * from '@/common/types';

export * from './editor/blocks';
export * from './editor/editor';
export * from './editor/inline';
export * from './editor/tunes';
export * from './code';
export * from './components';
export * from './field';
export * from './package';
export * from './page';
export * from './search';
export * from './shared';
export * from './switcher';
export * from './system';
export * from './undo';

import {
	KeyValueMap,
	PageController,
	ComponentController,
	SharedController,
} from '@/common';
import { PageMetaData, InputElement } from '.';

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

export interface AutocompleteItem {
	element: HTMLElement;
	value: string;
	item: KeyValueMap;
}

export interface AutocompleteItemData {
	value: string;
	title: string;
}

export interface BindingOptions {
	input?: InputElement;
	modifier?: Function;
	initial?: any;
	onChange?: (value: string) => void;
}

export interface DeduplicationSettings {
	getFormData: (element: HTMLElement) => KeyValueMap;
	enabled: boolean;
}

export interface Image {
	name: string;
	thumbnail: string;
}

export interface JumpbarItemData {
	value: string;
	title: string;
	icon: string;
	subtitle?: string;
	target?: string;
	external?: string;
	cls?: string[];
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

export interface Listener {
	remove: () => void;
}

export interface Logger {
	error: (...args: any[]) => void;
	log: (...args: any[]) => void;
	request: (url: string, data: KeyValueMap) => void;
	response: (url: string, data: KeyValueMap) => void;
	bindingInfo: (label: string, data: KeyValueMap) => void;
	bindingSuccess: (text: string) => void;
}

export interface NavTreeItem {
	wrapper: HTMLElement;
	summary: HTMLElement;
	children: HTMLElement;
	page: PageMetaData;
}

export interface NotifyOptions {
	message: string;
	icon: string;
	duration: number;
	className?: string;
}

export interface PackageDirectoryItems {
	pagelist: string[];
	filelist: string[];
	snippets: string[];
}

export interface Partials {
	[key: string]: string;
}

type PublishController =
	| PageController
	| SharedController
	| ComponentController;

export interface PublishControllers {
	state: PublishController;
	discard: PublishController;
	publish: PublishController;
}

export type PublicationState = 'draft' | 'published';

export interface SelectComponentOption {
	value: string | number;
	text?: string;
}

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
