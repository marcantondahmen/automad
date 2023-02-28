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

import EditorJS, { EditorConfig } from '@editorjs/editorjs';
import { SectionBlock } from '../components/Fields/Editor/Blocks/Section';
import { LayoutTune } from '../components/Fields/Editor/Tunes/Layout';
import { EditorOutputData } from '../types';

// @ts-ignore
import Header from '@editorjs/header';

export const createEditor = (
	data: EditorOutputData,
	config: EditorConfig,
	isSectionBlock: boolean
): EditorJS => {
	const editor = new EditorJS(
		Object.assign(
			{
				data,
				logLevel: 'ERROR',
				minHeight: 50,
				autofocus: false,
				tools: {
					layout: {
						class: LayoutTune,
						config: {
							isSectionBlock,
						},
					},
					header: Header,
					section: SectionBlock,
				},
				tunes: ['layout'],
				onReady: (): void => {
					data.blocks?.forEach((_block) => {
						const block = editor.blocks.getById(_block.id);
						const layout = _block.tunes?.layout;

						LayoutTune.apply(block, layout);
					});
				},
			},
			config
		)
	);

	return editor;
};
