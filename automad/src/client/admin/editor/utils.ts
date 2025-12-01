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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { BlockAPI, OutputBlockData } from '@/admin/vendor/editorjs';
import { BaseEditor, EditorOutputData, KeyValueMap } from '@/admin/types';
import { App, getLogger } from '../core';
import { nanoid } from 'nanoid';

/**
 * Handle unknown block data.
 *
 * @param block
 * @return the handled block
 */
export const unknownBlockHandler = (
	block: OutputBlockData
): OutputBlockData => {
	getLogger().log('Handling unknown block', block);

	if (block.type == 'section') {
		block.type = 'layoutSection';

		return block;
	}

	return null;
};

/**
 * Filter out empty data from an object.
 *
 * @param data
 * @return the filtered object
 */
export const filterEmptyData = <T>(data: T): Partial<T> => {
	const filtered: Partial<T> = {};

	for (const [key, value] of Object.entries(data)) {
		if (!!value || value === false) {
			filtered[key as keyof T] = value;
		}
	}

	return filtered;
};

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

/**
 * Remove shared component blocks that have been deleted.
 *
 * @param data
 * @return The filtered data
 */
export const removeDeleteComponents = (
	data: EditorOutputData
): EditorOutputData => {
	const componentIds = App.components.map((component) => component.id);

	return {
		...data,
		blocks:
			data?.blocks?.filter((block) => {
				return (
					block.type != 'component' ||
					componentIds.includes(block.data.id ?? '')
				);
			}) ?? [],
	};
};
