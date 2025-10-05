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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import SortableTree, {
	SortableTreeKeyValue,
	SortableTreeRenderLabelFunction,
} from 'sortable-tree';
import {
	App,
	Attr,
	CSS,
	EventName,
	getPageURL,
	html,
	query,
} from '@/admin/core';
import { createSortableTreeNodes, treeStyles } from '@/admin/core/tree';
import { PageMetaData } from '@/admin/types';
import { BaseComponent } from '@/admin/components/Base';
import { ModalComponent } from './Modal/Modal';

/**
 * The render function that renders the label HTML.
 *
 * @param data
 * @returns the rendered HTML
 */
const renderLabelFunction: SortableTreeRenderLabelFunction = (
	data: SortableTreeKeyValue
): string => {
	const icon = data.private ? 'eye-slash-fill' : 'folder2';

	return html`
		<label class="${CSS.navItem}">
			<input
				type="radio"
				name="targetPage"
				value="${data.url}"
				tabindex="0"
			/>
			<span class="${CSS.navLink}">
				<am-icon-text
					${Attr.icon}="${icon}"
					${Attr.text}="$${data.title}"
				></am-icon-text>
			</span>
		</label>
	`;
};

/**
 * An page selection tree field.
 *
 * @example
 * <am-page-select ${Attr.hideCurrent}></am-page-select>
 *
 * @extends BaseComponent
 */
class PageSelectTreeComponent extends BaseComponent {
	/**
	 * The sortable tree instance.
	 */
	private tree: SortableTree;

	/**
	 * True if the current page should be excluded.
	 */
	private get hideCurrent(): boolean {
		return this.hasAttribute(Attr.hideCurrent);
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.listen(window, EventName.appStateChange, this.init.bind(this));

		this.init();
	}

	/**
	 * Init the navTree.
	 */
	private init(): void {
		this.tree?.destroy();
		this.innerHTML = '';

		const pages: PageMetaData[] = this.filterPages(
			Object.values(App.pages) as PageMetaData[]
		);

		const nodes = createSortableTreeNodes(pages);

		this.tree = new SortableTree({
			nodes,
			element: this,
			initCollapseLevel: 1,
			disableSorting: true,
			styles: treeStyles,
			renderLabel: renderLabelFunction,
		});

		const currentNode =
			this.tree.findNode('url', getPageURL()) ||
			this.tree.findNode('url', '/');

		currentNode.reveal();

		const modal = this.closest<ModalComponent>(ModalComponent.TAG_NAME);

		if (modal) {
			this.listen(modal, EventName.modalOpen, () => {
				query<HTMLInputElement>('input', currentNode.label).checked =
					true;
			});
		}
	}

	/**
	 * Optionally define a filter function for the pages array.
	 *
	 * @param pages
	 * @returns the array of filtered pages
	 */
	private filterPages(pages: PageMetaData[]): PageMetaData[] {
		if (this.hideCurrent) {
			const current = getPageURL();
			const regex = new RegExp(`^${current}(\/|$)`);

			return pages.filter((page: PageMetaData) => {
				return page.url.match(regex) == null;
			});
		}

		return pages;
	}

	/**
	 * Remove all event listeners and observers when disconnecting.
	 */
	disconnectedCallback(): void {
		this.tree.destroy();
		super.disconnectedCallback();
	}
}

customElements.define('am-page-select-tree', PageSelectTreeComponent);
