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

import { AiAssistance } from '@/admin/editor/plugins/AiAssistance';
import { BlockToolData } from 'automad-editorjs';
import { EditorJSComponent } from '@/admin/components/EditorJS';
import { EditorJS } from '@/vendor/editorjs';

export interface AiTarget {
	text: string;
	blocks: BlockToolData[];
}

export type SelectedBlocks = EditorJS['blockSelection']['selectedBlocks'];

export interface AiRuntimeState {
	plugin: AiAssistance;
	component: EditorJSComponent;
	lastFocusedBlockIndex: number;
	selectedBlocks: SelectedBlocks;
	selectedRange: Range;
	selectionDisplay: string;
}
