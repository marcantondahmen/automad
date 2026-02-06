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
 * Copyright (c) 2023-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import {
	SortableTreeKeyValue,
	SortableTreeNodeData,
	SortableTreeStyles,
} from '@/vendor/sortable-tree';
import { PageMetaData } from '@/admin/types';
import { App, CSS } from '.';

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
	tree: CSS.tree,
	node: CSS.treeNode,
	nodeHover: CSS.treeNodeHover,
	nodeDragging: CSS.treeNodeDragging,
	nodeDropBefore: CSS.treeNodeDropBefore,
	nodeDropInside: CSS.treeNodeDropInside,
	nodeDropAfter: CSS.treeNodeDropAfter,
	label: CSS.treeLabel,
	subnodes: CSS.treeSubnodes,
	collapse: CSS.treeCollapse,
};
