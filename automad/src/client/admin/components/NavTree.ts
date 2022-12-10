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
 * Copyright (c) 2021-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
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
	CSS,
	Attr,
} from '../core';
import { KeyValueMap, NavTreeItem, PageMetaData } from '../types';
import { BaseComponent } from './Base';
import Sortable, { SortableEvent } from 'sortablejs';

/**
 * The navigation tree component.
 *
 * @example
 * <am-nav-tree ${Attr.hideCurrent}></am-nav-tree>
 *
 * @extends BaseComponent
 */
export class NavTreeComponent extends BaseComponent {
	/**
	 * The api endpoint.
	 */
	protected api = 'UI/navTree';

	/**
	 * The main CSS classes.
	 */
	protected elementClasses = [CSS.nav];

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
		this.classList.add(...this.elementClasses);

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

		const pages: PageMetaData[] = this.filterPages(
			Object.values(App.pages) as PageMetaData[]
		);

		this.renderLabel(pages.length);

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
		this.scrollToActive();
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
					details.setAttribute('open', '');
				}, 500);
			});

			listen(details, 'dragleave', (event: MouseEvent) => {
				if (enterTimeout) {
					clearTimeout(enterTimeout);
				}
			});
		});

		const childrenContainers = queryAll(
			`.${CSS.navChildren}`,
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

					ghostClass: CSS.navItemGhost,
					chosenClass: CSS.navItemChosen,
					dragClass: CSS.navItemDrag,

					onStart: () => {
						this.classList.add(CSS.navDragging);
					},

					onEnd: async (event: SortableEvent) => {
						this.classList.remove(CSS.navDragging);

						const { item, from, to } = event;

						const fromUrl = from.getAttribute(Attr.url);

						const toUrl = to.getAttribute(Attr.url);
						const toPath = to.getAttribute(Attr.path);
						const toChildren: string[] = [];

						Array.from(to.children).forEach((child) => {
							toChildren.push(child.getAttribute(Attr.path));
						});

						let redirect = null;

						if (fromUrl != toUrl) {
							this.sortables.forEach((sortable) => {
								sortable.destroy();
							});

							const data = await requestAPI('Page/move', {
								url: item.getAttribute(Attr.url),
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
	 *
	 * @param count
	 */
	protected renderLabel(count: number): void {
		create('span', [CSS.navLabel], {}, this).innerHTML = html`
			<span class="${CSS.flex} ${CSS.flexGap} ${CSS.flexAlignCenter}">
				$${App.text('sidebarPages')} &mdash; ${count}
			</span>
		`;
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
			[CSS.navItem],
			{
				[Attr.path]: page.path,
				[Attr.url]: page.url,
			},
			parent
		);
		const summary = create('summary', [CSS.navLink], {}, wrapper);
		const children = create(
			'div',
			[CSS.navChildren],
			{
				[Attr.path]: page.path,
				[Attr.url]: page.url,
			},
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
				[Attr.target]: `${Routes.page}?url=${encodeURIComponent(
					page.url
				)}`,
				[Attr.tooltip]: page.url,
				[Attr.tooltipOptions]:
					'placement: right, delay: 10, marginRight: 10',
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
			<am-icon-text
				${Attr.icon}="${icon}"
				${Attr.text}="$${page.title}"
			></am-icon-text>
			<span class="${CSS.navGrip}">${grabIcon}</span>
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
			CSS.navItemActive,
			item.page.url == url && isActivePage(Routes.page)
		);
	}

	/**
	 * Unfold the tree to reveal the active item.
	 */
	private unfoldToActive(): void {
		const activeItem = query(`.${CSS.navItemActive}`, this) as HTMLElement;

		if (activeItem) {
			queryParents('details', activeItem).forEach((item: HTMLElement) => {
				item.setAttribute('open', '');
			});
		}
	}

	/**
	 * Scroll parent container until active element is active.
	 */
	private scrollToActive(): void {
		const activeItem = query(`.${CSS.navItemActive}`, this) as HTMLElement;

		if (activeItem) {
			activeItem.scrollIntoView({
				block: 'end',
			});
		}
	}

	/**
	 * Toggle visibility of the children arrow indicators depending on the existance of children.
	 */
	private toggleChildrenIcons(): void {
		const childrenContainers = queryAll(`.${CSS.navChildren}`, this);

		childrenContainers.forEach((item: Element) => {
			(item.previousSibling as Element).classList.toggle(
				CSS.navLinkHasChildren,
				item.childElementCount > 0
			);
		});
	}
}

customElements.define('am-nav-tree', NavTreeComponent);
