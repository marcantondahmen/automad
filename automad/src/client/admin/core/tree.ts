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

import {
	SortableTreeKeyValue,
	SortableTreeNodeData,
	SortableTreeStyles,
} from 'sortable-tree';
import { PageMetaData } from '../types';
import { App } from './app';

/**
 * Create the nodes object that is used to build a SortableTree instance.
 *
 * @param pages
 * @returns the nested array of nodes
 */
export const createSortableTreeNodes = (
	pages: PageMetaData[]
): SortableTreeNodeData[] => {
	const parentMap = createParentMap(pages);

	const createNode = (
		page: PageMetaData,
		nodes: SortableTreeNodeData[]
	): SortableTreeNodeData[] => {
		const children = parentMap[page.url] || [];
		const subnodes: SortableTreeNodeData[] = [];
		const data: SortableTreeKeyValue =
			page as unknown as SortableTreeKeyValue;

		children.sort((a: PageMetaData, b: PageMetaData) =>
			a.index > b.index ? 1 : b.index > a.index ? -1 : 0
		);

		children.forEach((child) => {
			createNode(child, subnodes);
		});

		nodes.push({
			data,
			nodes: subnodes,
		});

		return nodes;
	};

	return createNode(App.pages['/'], []);
};

/**
 * Create a map where all pages are grouped by their direct parent page URL.
 *
 * @param pages
 * @returns the url/page[] map
 */
const createParentMap = (
	pages: PageMetaData[]
): { [key: string]: PageMetaData[] } => {
	const parentMap: { [key: string]: PageMetaData[] } = {};

	pages.forEach((page) => {
		const parent = page.parentUrl;

		if (parent) {
			if (typeof parentMap[parent] === 'undefined') {
				parentMap[parent] = [];
			}

			parentMap[parent].push(page);
		}
	});

	return parentMap;
};

/**
 * The custom classes that are used for the sortable tree.
 */
export const treeStyles: SortableTreeStyles = {
	tree: 'am-c-tree',
	node: 'am-c-tree__node',
	nodeHover: 'am-c-tree__node--hover',
	nodeDragging: 'am-c-tree__node--dragging',
	nodeDropBefore: 'am-c-tree__node--drop-before',
	nodeDropInside: 'am-c-tree__node--drop-inside',
	nodeDropAfter: 'am-c-tree__node--drop-after',
	label: 'am-c-tree__label',
	subnodes: 'am-c-tree__subnodes',
	collapse: 'am-c-tree__collapse',
};
