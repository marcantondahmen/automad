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
 * <am-nav-tree pages='[
 *     { "url": "/", "title": "Home", "path": "/", "parent": ""},
 *     { "url": "/page-1", "title": "Page 1", "path": "/01.page-1", "parent": "/"},
 *     ...
 * ]'></am-nav-tree>
 *
 * <am-nav-item view="System" icon="sliders" text="System ..."></am-nav-item>
 */

class NavTree extends BaseComponent {
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
			[this.cls.navItem],
			{ style: `--level: ${level}` },
			parent
		);
		const link = create('summary', [this.cls.navLink], {}, wrapper);
		const children = create('div', [this.cls.navChildren], {}, wrapper);
		const searchParams = new URLSearchParams(window.location.search);

		wrapper.classList.toggle(
			this.cls.navItemActive,
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
		const activeItem = query(`.${this.cls.navItemActive}`);

		if (activeItem) {
			queryParents('details', activeItem).forEach((item) => {
				item.setAttribute('open', true);
			});
		}
	}

	toggleChildrenIcons() {
		const childrenContainers = queryAll(`.${this.cls.navChildren}`, this);

		childrenContainers.forEach((item) => {
			item.previousSibling.classList.toggle(
				this.cls.navLinkHasChildren,
				item.childElementCount
			);
		});
	}
}

class NavItem extends BaseComponent {
	static get observedAttributes() {
		return ['view', 'icon', 'text'];
	}

	connectedCallback() {
		const searchParams = new URLSearchParams(window.location.search);

		this.classList.add(this.cls.navItem);
		this.innerHTML = this.render();

		this.classList.toggle(
			this.cls.navItemActive,
			this.elementAttributes.view == searchParams.get('view')
		);
	}

	render() {
		return `
			<a 
			href="?view=${this.elementAttributes.view}" 
			class="${this.cls.navLink}"
			>
				<i class="bi bi-${this.elementAttributes.icon}"></i>
				${this.elementAttributes.text}
			</a>
		`;
	}
}

customElements.define('am-nav-tree', NavTree);
customElements.define('am-nav-item', NavItem);
