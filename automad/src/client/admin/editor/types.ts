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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import type { EditorJS } from '@/vendor/editorjs';
import type { API, BlockAPI, OutputData, ToolConfig } from '@/vendor/editorjs';
import type { EditorJSComponent } from '@/admin/components/EditorJS';
import type { AiAssistance } from './plugins/AiAssistance';

export interface AiRuntimeState {
	plugin: AiAssistance;
	component: EditorJSComponent;
	lastFocusedBlockIndex: number;
	selectedBlocks: EditorJS['blockSelection']['selectedBlocks'];
	selectedRange: Range;
	selectionDisplay: string;
}

export type AttributeTuneData = string;

export interface BlockTuneConstructorOptions {
	api: API;
	config?: ToolConfig;
	block: BlockAPI;
	data: any;
}

export interface EditorOutputData extends OutputData {
	automadVersion?: string;
}
