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

import { API, BlockAPI, ToolConfig } from 'automad-editorjs';
import { CodeLanguage, EditorOutputData } from '..';
import {
	sectionBackgroundBlendModes,
	sectionBorderStyles,
} from '@/admin/editor/blocks/LayoutSection';
import { sliderEffects } from '@/admin/editor/blocks/ImageSlideshow';
import { buttonsJustifyOptions } from '@/admin/editor/blocks/Buttons';
import { tableOfContentsTypes } from '@/admin/editor/blocks/TableOfContents';
import { pagelistTypes } from '@/admin/editor/blocks/Pagelist';

export interface BlockTuneConstructorOptions {
	api: API;
	config?: ToolConfig;
	block: BlockAPI;
	data: any;
}

export interface ButtonsBlockButtonStyle {
	color?: string;
	background?: string;
	borderColor?: string;
	hoverColor?: string;
	hoverBackground?: string;
	hoverBorderColor?: string;
	borderWidth?: string;
	borderRadius?: string;
	paddingHorizontal?: string;
	paddingVertical?: string;
}

export interface ButtonsBlockData {
	justify: (typeof buttonsJustifyOptions)[number];
	gap: string;
	primaryText: string;
	primaryLink: string;
	primaryStyle: ButtonsBlockButtonStyle;
	primaryOpenInNewTab: boolean;
	secondaryText?: string;
	secondaryLink?: string;
	secondaryStyle?: ButtonsBlockButtonStyle;
	secondaryOpenInNewTab?: boolean;
}

export interface CalloutBlockData {
	title: string;
	text: string;
}

export interface CalloutBlockInputs {
	title: HTMLDivElement;
	text: HTMLDivElement;
}

export interface CodeBlockData {
	code: string;
	language: CodeLanguage;
	lineNumbers: boolean;
}

export interface CollapsibleSectionBlockData {
	title: string;
	content: EditorOutputData;
	group: string;
	collapsed: boolean;
}

export interface FilelistBlockData {
	file: string;
	glob: string;
	sortOrder: 'asc' | 'desc';
}

export interface HeaderBlockData {
	level: 1 | 2 | 3 | 4 | 5 | 6;
	text: string;
}

export interface ImageBlockData {
	url: string;
	caption: string;
	alt: string;
	link: string;
	openInNewTab: boolean;
}

export type GalleryLayout = 'columns' | 'rows' | 'grid';

export interface GalleryBlockData {
	files: string[];
	layout: GalleryLayout;
	columnWidthPx: number;
	rowHeightPx: number;
	gapPx: number;
	fillRectangle: boolean;
}

export interface MailBlockData {
	to: string;
	error: string;
	success: string;
	errorAddress: string;
	labelAddress: string;
	errorSubject: string;
	labelSubject: string;
	errorBody: string;
	labelBody: string;
	labelSend: string;
}

export interface PagelistBlockData {
	file: string;
	context: string;
	sortField: string;
	sortOrder: 'asc' | 'desc';
	type: (typeof pagelistTypes)[number];
	excludeHidden: boolean;
	excludeCurrent: boolean;
	matchUrl: string;
	filter: string;
	template: string;
	limit: number;
	offset: number;
}

export interface ParagraphBlockData {
	text: string;
	large: boolean;
}

export interface QuoteBlockData {
	text: string;
	caption: string;
}

export interface QuoteBlockInputs {
	text: HTMLDivElement;
	caption: HTMLDivElement;
}

export interface RawBlockData {
	code: string;
}

export type SectionToolbarRadioOptions<T extends string> = {
	[key in T]: { icon: string; tooltip: string };
};

export type SectionJustifyContentOption =
	| 'start'
	| 'center'
	| 'end'
	| 'space-between'
	| 'space-evenly'
	| 'fill-row';

export type SectionAlignItemsOption = 'start' | 'center' | 'end' | 'stretch';

export type SectionBackgroundBlendMode =
	(typeof sectionBackgroundBlendModes)[number];

export type SectionBorderStyle = (typeof sectionBorderStyles)[number];

export interface SectionStyle {
	card: boolean;
	shadow: boolean;
	color: string;
	backgroundColor: string;
	backgroundBlendMode: SectionBackgroundBlendMode;
	borderColor: string;
	borderWidth: string;
	borderRadius: string;
	borderStyle: SectionBorderStyle;
	backgroundImage: string;
	paddingTop: string;
	paddingBottom: string;
	overflowHidden: boolean;
}

export interface LayoutSectionBlockData {
	content: EditorOutputData;
	style: SectionStyle;
	justify: SectionJustifyContentOption;
	align: SectionAlignItemsOption;
	gap: string;
	minBlockWidth: string;
}

export interface ComponentBlockData {
	id: string;
}

export interface ImageSlideshowBreakpoint {
	slidesPerView: number;
}

export interface ImageSlideshowBreakpoints {
	[minWidth: string]: ImageSlideshowBreakpoint;
}

export interface ImageSlideshowBlockData {
	files: string[];
	imageWidthPx: number;
	imageHeightPx: number;
	gapPx: number;
	slidesPerView: number;
	loop: boolean;
	autoplay: boolean;
	effect: (typeof sliderEffects)[number];
	breakpoints: ImageSlideshowBreakpoints;
}

export interface SnippetBlockData {
	file: string;
	snippet: string;
}

export interface TableOfContentsBlockData {
	type: (typeof tableOfContentsTypes)[number];
}

export interface VideoBlockData {
	url: string;
	autoplay: boolean;
	loop: boolean;
	controls: boolean;
	muted: boolean;
	caption: string;
}
