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
	listen,
	requestAPI,
	eventNames,
} from '../core';
import { KeyValueMap, NavTreeItem, PageMetaData } from '../types';
import { BaseComponent } from './Base';
import Sortable, { SortableEvent } from 'sortablejs';

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
	protected tree: KeyValueMap;

	/**
	 * True if the tree can be sorted.
	 */
	protected isSortable: boolean = true;

	/**
	 * The array of Sortable instances.
	 */
	private sortables: Sortable[] = [];

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(classes.nav);

		this.listeners.push(
			listen(window, eventNames.appStateChange, this.init.bind(this))
		);

		this.init();
	}

	/**
	 * Init the navTree.
	 */
	protected init(): void {
		this.innerHTML = '';
		this.tree = {};

		this.renderLabel();

		const pages: PageMetaData[] = this.filterPages(
			Object.values(App.pages) as PageMetaData[]
		);

		let parent: HTMLElement;

		pages.sort((a: KeyValueMap, b: KeyValueMap) =>
			a.index > b.index ? 1 : b.index > a.index ? -1 : 0
		);

		pages.forEach((page) => {
			if (typeof this.tree[page.parentPath] == 'undefined') {
				parent = this;
			} else {
				parent = this.tree[page.parentPath].children;
			}

			const item = this.createItem(page, parent);
			this.tree[page.path] = item;

			this.toggleItem(item, getPageURL());
		});

		this.unfoldToActive();
		this.toggleChildrenIcons();
		this.initSortable();
	}

	/**
	 * Init Sortable JS.
	 *
	 * @see {@link github https://github.com/SortableJS/Sortable}
	 */
	private initSortable(): void {
		if (!this.isSortable) {
			return;
		}

		queryAll('details', this).forEach((details: HTMLElement) => {
			let enterTimeout: NodeJS.Timer;

			listen(details, 'dragenter', (event: MouseEvent) => {
				if (details.contains(event.relatedTarget as HTMLElement)) {
					return;
				}

				enterTimeout = setTimeout(() => {
					details.toggleAttribute('open');
				}, 750);
			});

			listen(details, 'dragleave', (event: MouseEvent) => {
				if (enterTimeout) {
					clearTimeout(enterTimeout);
				}
			});
		});

		const childrenContainers = queryAll(
			`.${classes.navChildren}`,
			this
		) as HTMLElement[];

		childrenContainers.forEach((container) => {
			this.sortables.push(
				new Sortable(container, {
					group: 'navTree',
					direction: 'vertical',

					animation: 200,

					emptyInsertThreshold: 0,

					swapThreshold: 0.5,
					invertedSwapThreshold: 0.5,
					invertSwap: true,

					ghostClass: classes.navItemGhost,
					chosenClass: classes.navItemChosen,
					dragClass: classes.navItemDrag,

					onStart: () => {
						this.classList.add(classes.navDragging);
					},

					onEnd: async (event: SortableEvent) => {
						this.classList.remove(classes.navDragging);

						const { item, from, to } = event;

						const fromUrl = from.getAttribute('url');

						const toUrl = to.getAttribute('url');
						const toPath = to.getAttribute('path');
						const toChildren: string[] = [];

						Array.from(to.children).forEach((child) => {
							toChildren.push(child.getAttribute('path'));
						});

						let redirect = null;

						if (fromUrl != toUrl) {
							this.sortables.forEach((sortable) => {
								sortable.destroy();
							});

							const data = await requestAPI('Page/move', {
								url: item.getAttribute('url'),
								targetPage: toUrl,
							});

							redirect = data.redirect;
						}

						await requestAPI('Page/updateIndex', {
							parentPath: toPath,
							layout: JSON.stringify(toChildren),
						});

						if (redirect) {
							App.root.setView(redirect);
						}
					},
				})
			);
		});
	}

	/**
	 * Render the tree label.
	 */
	protected renderLabel(): void {
		create('span', [classes.navLabel], {}, this).innerHTML =
			App.text('sidebarPages');
	}

	/**
	 * Optionally define a filter function for the pages array.
	 *
	 * @param pages
	 * @returns the array of filtered pages
	 */
	protected filterPages(pages: PageMetaData[]): PageMetaData[] {
		return pages;
	}

	/**
	 * Create a tree item.
	 *
	 * @param page - the page data object
	 * @param parent - the children container of the parent tree node
	 * @returns the NavItem object
	 */
	private createItem(page: PageMetaData, parent: HTMLElement): NavTreeItem {
		const level = (page.path.match(/\/./g) || []).length;
		const wrapper = create(
			'details',
			[classes.navItem],
			{
				path: page.path,
				url: page.url,
				title: page.path,
			},
			parent
		);
		const summary = create('summary', [classes.navLink], {}, wrapper);
		const children = create(
			'div',
			[classes.navChildren],
			{ url: page.url, path: page.path },
			wrapper
		);

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
				target: `${Routes.page}?url=${encodeURIComponent(page.url)}`,
			},
			summary
		);

		let icon = 'file-earmark-text';
		let grabIcon = '';

		if (page.private) {
			icon = 'eye-slash-fill';
		}

		if (this.isSortable) {
			grabIcon = '<i class="bi bi-grip-vertical"></i>';
		}

		link.innerHTML = html`
			<am-icon-text icon="${icon}" text="${page.title}"></am-icon-text>
			<span class="${classes.navGrip}">${grabIcon}</span>
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
			item.page.url == url && isActivePage(Routes.page)
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
