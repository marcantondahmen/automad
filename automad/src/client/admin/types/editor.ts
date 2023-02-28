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

import { API, BlockAPI, OutputData, ToolConfig } from '@editorjs/editorjs';
import { KeyValueMap } from '.';
import {
	SectionBackgroundBlendModes,
	SectionJustifyContentOptions,
} from '../components/Fields/Editor/Blocks/Section';
import { fractions } from '../components/Fields/Editor/Tunes/Layout';

export interface BlockTuneConstructorOptions {
	api: API;
	config?: ToolConfig;
	block: BlockAPI;
	data: any;
}

export interface EditorOutputData extends OutputData {
	automadVersion: string;
}

export interface LayoutTuneData {
	stretched: boolean;
	width: typeof fractions;
}

export type SectionJustifyContentOption =
	keyof typeof SectionJustifyContentOptions;

export type SectionBackgroundBlendMode =
	typeof SectionBackgroundBlendModes[number];

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
	backgroundImage: string;
	paddingTop: string;
	paddingBottom: string;
	overflowHidden: boolean;
}

export interface SectionBlockData {
	content: KeyValueMap;
	style: SectionStyle;
	justify: SectionJustifyContentOption;
	gap: string;
	minBlockWidth: string;
}
