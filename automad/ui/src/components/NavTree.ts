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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	classes,
	create,
	App,
	getPageURL,
	isActivePage,
	query,
	queryParents,
	queryAll,
	Routes,
	html,
} from '../core';
import { KeyValueMap, NavTreeItem, NavTreePageData } from '../types';
import { BaseComponent } from './Base';

/**
 * The navigation tree component.
 *
 * @example
 * <am-nav-tree hidecurrent></am-nav-tree>
 *
 * @extends BaseComponent
 */
export class NavTreeComponent extends BaseComponent {
	/**
	 * The api endpoint.
	 */
	protected api = 'UI/navTree';

	/**
	 * The pages tree structure.
	 */
	protected tree: KeyValueMap = {};

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(classes.nav);

		create('span', [classes.navLabel], {}, this).innerHTML =
			App.text('sidebarPages');

		this.init();
	}

	/**
	 * Init the navTree.
	 */
	protected init(): void {
		const pages: NavTreePageData[] = this.filterPages(
			Object.values(App.pages) as NavTreePageData[]
		);

		let parent: HTMLElement;

		pages.sort((a: KeyValueMap, b: KeyValueMap) =>
			a.path > b.path ? 1 : b.path > a.path ? -1 : 0
		);

		pages.forEach((page) => {
			if (typeof this.tree[page.parent] == 'undefined') {
				parent = this;
			} else {
				parent = this.tree[page.parent].children;
			}

			const item = this.createItem(page, parent);
			this.tree[page.url] = item;

			this.toggleItem(item, getPageURL());
		});

		this.unfoldToActive();
		this.toggleChildrenIcons();
	}

	/**
	 * Optionally define a filter function for the pages array.
	 *
	 * @param pages
	 * @returns the array of filtered pages
	 */
	protected filterPages(pages: NavTreePageData[]): NavTreePageData[] {
		return pages;
	}

	/**
	 * Create a tree item.
	 *
	 * @param page - the page data object
	 * @param parent - the children container of the parent tree node
	 * @returns the NavItem object
	 */
	private createItem(
		page: NavTreePageData,
		parent: HTMLElement
	): NavTreeItem {
		const level = (page.path.match(/\/./g) || []).length;
		const wrapper = create(
			'details',
			[classes.navItem],
			{ style: `--level: ${level}` },
			parent
		);
		const summary = create('summary', [classes.navLink], {}, wrapper);
		const children = create('div', [classes.navChildren], {}, wrapper);

		if (!level) {
			wrapper.setAttribute('open', true);
		}

		const item: NavTreeItem = {
			wrapper,
			summary,
			children,
			page,
		};

		this.createSummaryChild(item);

		return item;
	}

	/**
	 * Render the actual content of a summary as a link.
	 *
	 * @param item
	 * @returns The actual tree item summery child
	 */
	protected createSummaryChild(item: NavTreeItem): string {
		const { page, summary } = item;
		const link = create(
			'am-link',
			[],
			{
				target: `${Routes[Routes.page]}?url=${encodeURIComponent(
					page.url
				)}`,
			},
			summary
		);

		let icon = 'file-earmark-text';

		if (page.private) {
			icon = 'file-earmark-lock2-fill';
		}

		link.innerHTML = html`
			<am-icon-text icon="${icon}" text="${page.title}"></am-icon-text>
		`;

		return link;
	}

	/**
	 * Toggle an item to be active or inactive.
	 *
	 * @param item
	 * @param url
	 */
	protected toggleItem(item: NavTreeItem, url: string): void {
		item.wrapper.classList.toggle(
			classes.navItemActive,
			item.page.url == url && isActivePage(Routes[Routes.page])
		);
	}

	/**
	 * Unfold the tree to reveal the active item.
	 */
	private unfoldToActive(): void {
		const activeItem = query(
			`.${classes.navItemActive}`,
			this
		) as HTMLElement;

		if (activeItem) {
			queryParents('details', activeItem).forEach((item: HTMLElement) => {
				item.setAttribute('open', '');
			});
		}
	}

	/**
	 * Toggle visibility of the children arrow indicators depending on the existance of children.
	 */
	private toggleChildrenIcons(): void {
		const childrenContainers = queryAll(`.${classes.navChildren}`, this);

		childrenContainers.forEach((item: Element) => {
			(item.previousSibling as Element).classList.toggle(
				classes.navLinkHasChildren,
				item.childElementCount > 0
			);
		});
	}
}

customElements.define('am-nav-tree', NavTreeComponent);
