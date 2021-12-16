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

import { query, queryAll, queryParents } from '../utils/core';
import { create } from '../utils/create';
import { BaseComponent } from './BaseComponent';

/**
 * <am-tree pages='[
 *     { "url": "/", "title": "Home", "path": "/", "parent": ""},
 *     { "url": "/page-1", "title": "Page 1", "path": "/01.page-1", "parent": "/"},
 *     ...
 * ]'></am-tree>
 */

class Tree extends BaseComponent {
	static get observedAttributes() {
		return ['pages'];
	}

	connectedCallback() {
		const pages = JSON.parse(this.elementAttributes.pages);
		const tree = {};

		this.removeAttribute('pages');

		pages.sort((a, b) => (a.path > b.path ? 1 : b.path > a.path ? -1 : 0));

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

	createItem(page, parent) {
		const level = (page.path.match(/\/./g) || []).length;
		const wrapper = create(
			'details',
			[this.cls.sideNavItem],
			{ style: `--level: ${level}` },
			parent
		);
		const link = create('summary', [this.cls.sideNavLink], {}, wrapper);
		const children = create('div', [this.cls.sideNavChildren], {}, wrapper);
		const searchParams = new URLSearchParams(window.location.search);

		wrapper.classList.toggle(
			this.cls.sideNavItemActive,
			page.url == searchParams.get('url')
		);

		if (!level) {
			wrapper.setAttribute('open', true);
		}

		link.innerHTML = `
			<a href="?view=Page&url=${encodeURIComponent(page.url)}">${page.title}</a>
		`;

		return { wrapper, link, children };
	}

	unfoldToActive() {
		const activeItem = query(`.${this.cls.sideNavItemActive}`);

		if (activeItem) {
			queryParents('details', activeItem).forEach((item) => {
				item.setAttribute('open', true);
			});
		}
	}

	toggleChildrenIcons() {
		const childrenContainers = queryAll(
			`.${this.cls.sideNavChildren}`,
			this
		);

		childrenContainers.forEach((item) => {
			item.previousSibling.classList.toggle(
				this.cls.sideNavLinkHasChildren,
				item.childElementCount
			);
		});
	}
}

customElements.define('am-tree', Tree);
