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

import { API, BlockAPI, ToolConfig } from '@editorjs/editorjs';
import { CodeLanguage, KeyValueMap } from '..';
import {
	sectionAlignItemsOptions,
	sectionBackgroundBlendModes,
	sectionBorderStyles,
	sectionJustifyContentOptions,
} from '@/editor/blocks/Section';

export interface BlockTuneConstructorOptions {
	api: API;
	config?: ToolConfig;
	block: BlockAPI;
	data: any;
}

export interface CodeBlockData {
	code: string;
	language: CodeLanguage;
}

export interface ImageBlockData {
	url: string;
	caption: string;
	link: string;
	openInNewTab: boolean;
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

export type SectionJustifyContentOption =
	keyof typeof sectionJustifyContentOptions;

export type SectionAlignItemsOption = keyof typeof sectionAlignItemsOptions;

export type SectionBackgroundBlendMode =
	(typeof sectionBackgroundBlendModes)[number];

export type SectionBorderStyle = (typeof sectionBorderStyles)[number];

export interface SectionStyle {
	card: boolean;
	shadow: boolean;
	matchRowHeight: boolean;
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

export interface SectionBlockData {
	content: KeyValueMap;
	style: SectionStyle;
	justify: SectionJustifyContentOption;
	align: SectionAlignItemsOption;
	gap: string;
	minBlockWidth: string;
}
