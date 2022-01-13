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
	getDashboardURL,
	query,
	queryAll,
	queryParents,
	text,
} from '../utils/core';
import { create } from '../utils/create';
import { requestController } from '../utils/request';
import { BaseComponent } from './BaseComponent';

/**
 * Test whether a view is active.
 * @param {string} view
 * @returns {boolean}
 */
const isActiveView = (view) => {
	const regex = new RegExp(`\/${view}\$`, 'i');
	return window.location.pathname.match(regex) != null;
};

/**
 * The navigation tree component.
 * ```
 * <am-nav-tree controller="UIController::navTree"></am-nav-tree>
 * ```
 *
 * @extends BaseComponent
 */
class NavTree extends BaseComponent {
	/**
	 * The controller.
	 *
	 * @type {string}
	 */
	controller = 'UIController::navTree';

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		this.classList.add(classes.nav);

		create('span', [classes.navLabel], {}, this).innerHTML = text(
			'sidebar_header_pages'
		);

		this.loading = create('div', [classes.navSpinner], {}, this);
		create('div', [classes.spinner], {}, this.loading);
		this.init();
	}

	/**
	 * Init the navTree.
	 */
	async init() {
		const response = await requestController(this.controller);
		const pages = response.data;
		const tree = {};
		let parent;

		pages.sort((a, b) => (a.path > b.path ? 1 : b.path > a.path ? -1 : 0));

		this.loading.remove();

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
			[classes.navItem],
			{ style: `--level: ${level}` },
			parent
		);
		const link = create('summary', [classes.navLink], {}, wrapper);
		const children = create('div', [classes.navChildren], {}, wrapper);
		const searchParams = new URLSearchParams(window.location.search);

		wrapper.classList.toggle(
			classes.navItemActive,
			page.url == searchParams.get('url') && isActiveView('Page')
		);

		if (!level) {
			wrapper.setAttribute('open', true);
		}

		let icon = 'file-earmark-text';

		if (page.private) {
			icon = 'file-earmark-lock2-fill';
		}

		link.innerHTML = `
			<a href="${getDashboardURL()}/Page?url=${encodeURIComponent(page.url)}">
				<i class="bi bi-${icon}"></i>
				<span>${page.title}</span>
			</a>
		`;

		return { wrapper, link, children };
	}

	/**
	 * Unfold the tree to reveal the active item.
	 */
	unfoldToActive() {
		const activeItem = query(`.${classes.navItemActive}`);

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
		const childrenContainers = queryAll(`.${classes.navChildren}`, this);

		childrenContainers.forEach((item) => {
			item.previousSibling.classList.toggle(
				classes.navLinkHasChildren,
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
		this.classList.add(classes.navItem);
		this.classList.toggle(
			classes.navItemActive,
			isActiveView(this.elementAttributes.view)
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
			link = `${getDashboardURL()}/${this.elementAttributes.view}`;
		}

		return `
			<a 
			href="${link}" 
			class="${classes.navLink}"
			>
				<i class="bi bi-${this.elementAttributes.icon}"></i>
				<span>${this.elementAttributes.text}</span>
			</a>
		`;
	}
}

customElements.define('am-nav-tree', NavTree);
customElements.define('am-nav-item', NavItem);
