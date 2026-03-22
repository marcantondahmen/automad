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
 * Copyright (c) 2024-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { BlockAPI, OutputBlockData } from '@/vendor/editorjs';
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
 * Sanitize and compare editor output data. Note that data returned by the API
 * use [] to represent both, empty arrays and empty objects. This function
 * removes empty objects and arrays during stringification in order to
 * correctly compare the content.
 *
 * @param a
 * @param b
 * @return true if both objects are the same
 */
export const outputIsEqual = (a: KeyValueMap, b: KeyValueMap): boolean => {
	// Remove empty arrays and objects that differ in shape
	// when being returned by the API.
	const replacer = (_: any, value: any): any => {
		if (Array.isArray(value) && value.length === 0) {
			return undefined;
		}

		if (
			value &&
			typeof value === 'object' &&
			!Array.isArray(value) &&
			Object.keys(value).length === 0
		) {
			return undefined;
		}

		return value;
	};

	return JSON.stringify(a, replacer) == JSON.stringify(b, replacer);
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
		if (!!value || value === false || value === '0' || value === 0) {
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
export const removeDeletedComponents = (
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
