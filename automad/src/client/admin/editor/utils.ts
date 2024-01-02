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
 * Copyright (c) 2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { KeyValueMap } from '@/types';
import { API, BlockAPI } from '@editorjs/editorjs';
import { nanoid } from 'nanoid';

/**
 * Insert an existing block including its tunes.
 * Can be used for duplication or moving blocks to other editors.
 *
 * @param block
 * @param editor
 * @param index
 */
export const insertBlock = async (
	block: BlockAPI,
	editor: { saver: API['saver']; blocks: API['blocks'] },
	index: number = 0
): Promise<void> => {
	const blockData = (await block.save()) as KeyValueMap;
	const editorData = await editor.saver.save();

	await editor.blocks.render({
		blocks: [
			...editorData.blocks.slice(0, index - 1),
			{
				data: blockData.data,
				type: blockData.tool,
				tunes: blockData.tunes,
				id: nanoid(10),
			},
			...editorData.blocks.slice(index - 1),
		],
	});

	editor.blocks.getBlockByIndex(0).dispatchChange();
};
