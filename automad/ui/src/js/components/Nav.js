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

import { getDashboardURL, query, queryAll, queryParents } from '../utils/core';
import { create } from '../utils/create';
import { BaseComponent } from './BaseComponent';

/**
 * The navigation tree component.
 * ```
 * <am-nav-tree pages='[
 *     { "url": "/", "title": "Home", "path": "/", "parent": ""},
 *     { "url": "/page-1", "title": "Page 1", "path": "/01.page-1", "parent": "/"},
 *     ...
 * ]'></am-nav-tree>
 * ```
 *
 * @extends BaseComponent
 */
class NavTree extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @type {Array}
	 * @static
	 */
	static get observedAttributes() {
		return ['pages'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
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

	/**
	 * Create a tree item.
	 *
	 * @param {Object} page - the page data object
	 * @param {HTMLElement} parent - the children container of the parent tree node
	 * @returns {{wrapper: HTMLElement, link: HTMLElement, children: HTMLElement}}
	 */
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
			<a href="${getDashboardURL}/Page?url=${encodeURIComponent(page.url)}">
				<i class="bi bi-file-earmark-text"></i>
				<span>${page.title}</span>
			</a>
		`;

		return { wrapper, link, children };
	}

	/**
	 * Unfold the tree to reveal the active item.
	 */
	unfoldToActive() {
		const activeItem = query(`.${this.cls.navItemActive}`);

		if (activeItem) {
			queryParents('details', activeItem).forEach((item) => {
				item.setAttribute('open', true);
			});
		}
	}

	/**
	 * Toggle visibility of the children arrow indicators depending on the existance of children.
	 */
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

/**
 * A simple link in the sidebar navigation.
 * ```
 * <am-nav-item view="System" icon="sliders" text="System"></am-nav-item>
 * ```
 *
 * @extends BaseComponent
 */
class NavItem extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @type {Array}
	 * @static
	 */
	static get observedAttributes() {
		return ['view', 'icon', 'text'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		const searchParams = new URLSearchParams(window.location.search);
		const activeView = searchParams.get('view') || '';

		this.classList.add(this.cls.navItem);
		this.classList.toggle(
			this.cls.navItemActive,
			this.elementAttributes.view == activeView
		);

		this.innerHTML = this.render();
	}

	/**
	 * Render the component.
	 *
	 * @returns {string} the rendered HTML
	 */
	render() {
		let link = './';

		if (this.elementAttributes.view) {
			link = `${getDashboardURL}/${this.elementAttributes.view}`;
		}

		return `
			<a 
			href="${link}" 
			class="${this.cls.navLink}"
			>
				<i class="bi bi-${this.elementAttributes.icon}"></i>
				<span>${this.elementAttributes.text}</span>
			</a>
		`;
	}
}

customElements.define('am-nav-tree', NavTree);
customElements.define('am-nav-item', NavItem);
