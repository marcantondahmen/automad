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

import { BaseEditor, KeyValueMap } from '@/types';
import { BlockAPI } from 'automad-editorjs';
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
	editor: BaseEditor,
	index: number = 0
): Promise<void> => {
	const blockData = (await block.save()) as KeyValueMap;

	editor.blocks.insert(
		blockData.tool,
		blockData.data,
		blockData.config,
		index,
		true,
		false,
		nanoid(32),
		blockData.tunes
	);

	editor.blocks.getBlockByIndex(0).dispatchChange();
};
