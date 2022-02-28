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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { classes, create, getPageURL, html, listen, query } from '../core';
import { NavTreeItem, NavTreePageData } from '../types';
import { NavTreeComponent } from './NavTree';

/**
 * An page selection tree field.
 *
 * @example
 * <am-page-select hidecurrent></am-page-select>
 *
 * @extends BaseComponent
 */
class PageSelectTreeComponent extends NavTreeComponent {
	/**
	 * True if the current page should be excluded.
	 */
	private get hideCurrent(): boolean {
		return this.hasAttribute('hidecurrent');
	}

	/**
	 * Render the tree label.
	 */
	protected renderLabel(): void {
		return;
	}

	/**
	 * True if a page should be highlighted on init.
	 *
	 * @param page
	 * @returns true if the page should be highlighted initially
	 */
	private isHighlightedOnInit(page: NavTreePageData): boolean {
		if (this.hideCurrent) {
			const parentOfActive = getPageURL()
				.replace(/[^\/]+$/, '')
				.replace(/(.)\/$/, '$1');
			return parentOfActive === page.url;
		}

		return getPageURL() == page.url;
	}

	/**
	 * Optionally define a filter function for the pages array.
	 *
	 * @param pages
	 * @returns the array of filtered pages
	 */
	protected filterPages(pages: NavTreePageData[]): NavTreePageData[] {
		if (this.hideCurrent) {
			const current = getPageURL();
			const regex = new RegExp(`^${current}(\/|$)`);

			return pages.filter((page: NavTreePageData) => {
				return page.url.match(regex) == null;
			});
		}

		return pages;
	}

	/**
	 * Render the actual content of a summary as a link.
	 *
	 * @param item
	 * @returns The actual tree item summery child
	 */
	protected createSummaryChild(item: NavTreeItem): string {
		const { page, summary } = item;
		const label = create('label', [], {}, summary);

		let icon = 'folder';

		if (page.private) {
			icon = 'folder-fill';
		}

		label.innerHTML = html`
			<am-icon-text icon="${icon}" text="${page.title}"></am-icon-text>
			<input
				class="${classes.displayNone}"
				type="radio"
				name="targetPage"
				value="${page.url}"
				${this.isHighlightedOnInit(page) ? 'checked' : ''}
			/>
		`;

		const radioInput = query('input', label) as HTMLInputElement;

		listen(radioInput, 'change', (): void => {
			Object.values(this.tree).forEach((_item: NavTreeItem) => {
				this.toggleItem(_item, null);
			});
		});

		return label;
	}

	/**
	 * Toggle an item to be active or inactive.
	 *
	 * @param item
	 * @param url
	 */
	protected toggleItem(item: NavTreeItem, url: string): void {
		const radioInput = query('input', item.summary) as HTMLInputElement;

		item.wrapper.classList.toggle(
			classes.navItemActive,
			radioInput.checked
		);
	}
}

customElements.define('am-page-select-tree', PageSelectTreeComponent);
