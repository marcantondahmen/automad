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
	getPageURL,
	isActiveView,
	listen,
	query,
	queryAll,
	queryParents,
} from '../core/utils';
import { create } from '../core/create';

import { KeyValueMap } from '../core/types';
import { BaseComponent } from './Base';
import { App } from '../core/app';

interface Page {
	url: string;
	title: string;
	path: string;
	parent: string;
	private: boolean;
}

interface NavItem {
	wrapper: HTMLElement;
	link: HTMLElement;
	children: HTMLElement;
}

/**
 * The navigation tree component.
 *
 * @example
 * <am-nav-tree controller="UIController::navTree"></am-nav-tree>
 *
 * @extends BaseComponent
 */
class NavTreeComponent extends BaseComponent {
	/**
	 * The controller.
	 */
	protected controller = 'UIController::navTree';

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(classes.nav);

		create('span', [classes.navLabel], {}, this).innerHTML = App.text(
			'sidebar_header_pages'
		);

		this.init();
	}

	/**
	 * Init the navTree.
	 */
	private init(): void {
		const pages: Page[] = App.navTree as Page[];
		const tree: KeyValueMap = {};
		let parent: HTMLElement;

		pages.sort((a: KeyValueMap, b: KeyValueMap) =>
			a.path > b.path ? 1 : b.path > a.path ? -1 : 0
		);

		pages.forEach((page) => {
			if (typeof tree[page.parent] == 'undefined') {
				parent = this;
			} else {
				parent = tree[page.parent].children;
			}

			tree[page.url] = this.createItem(page, parent);
		});

		this.unfoldToActive();
		this.toggleChildrenIcons();
	}

	/**
	 * Create a tree item.
	 *
	 * @param page - the page data object
	 * @param parent - the children container of the parent tree node
	 * @returns the NavItem object
	 */
	private createItem(page: Page, parent: HTMLElement): NavItem {
		const level = (page.path.match(/\/./g) || []).length;
		const wrapper = create(
			'details',
			[classes.navItem],
			{ style: `--level: ${level}` },
			parent
		);
		const link = create('summary', [classes.navLink], {}, wrapper);
		const children = create('div', [classes.navChildren], {}, wrapper);

		wrapper.classList.toggle(
			classes.navItemActive,
			page.url == getPageURL() && isActiveView('Page')
		);

		if (!level) {
			wrapper.setAttribute('open', true);
		}

		let icon = 'file-earmark-text';

		if (page.private) {
			icon = 'file-earmark-lock2-fill';
		}

		link.innerHTML = `
			<am-link target="Page?url=${encodeURIComponent(page.url)}">
				<i class="bi bi-${icon}"></i>
				<span>${page.title}</span>
			</am-link>
		`;

		return { wrapper, link, children };
	}

	/**
	 * Unfold the tree to reveal the active item.
	 */
	private unfoldToActive(): void {
		const activeItem = query(`.${classes.navItemActive}`) as HTMLElement;

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
