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

import { AutocompleteComponent } from './Autocomplete';
import { JumpbarItemData, KeyValueMap, PageMetaData } from '../types';
import { App, keyCombo, create, classes, html, Routes } from '../core';

const searchData = (): JumpbarItemData[] => {
	return [
		{
			target: Routes[Routes.search],
			value: App.text('searchTitle'),
			title: App.text('searchTitle'),
			icon: 'search',
		},
	];
};

const inPageData = (): JumpbarItemData[] => {
	return [
		{
			external: App.baseURL,
			value: App.text('inPageEdit'),
			title: App.text('inPageEdit'),
			icon: 'window-desktop',
		},
	];
};

const settingsData = (): JumpbarItemData[] => {
	const item = (
		section: string,
		title: string,
		icon: string
	): JumpbarItemData => {
		return {
			target: `${Routes[Routes.system]}?section=${
				App.sections.system[section]
			}`,
			value: `${App.text('systemTitle')} ${App.text(title)}`,
			title: App.text(title),
			icon,
		};
	};

	const data: JumpbarItemData[] = [
		item('cache', 'systemCache', 'stack'),
		item('users', 'systemUsers', 'people'),
		item('update', 'systemUpdate', 'arrow-repeat'),
		item('feed', 'systemRssFeed', 'rss'),
		item('language', 'systemLanguage', 'translate'),
		item('headless', 'systemHeadless', 'braces'),
		item('debug', 'systemDebug', 'bug'),
		item('config', 'systemConfigFile', 'file-earmark-code'),
	];

	return data;
};

const sharedData = (): JumpbarItemData[] => {
	return [
		{
			target: Routes[Routes.shared],
			value: App.text('sharedTitle'),
			title: App.text('sharedTitle'),
			icon: 'file-earmark-medical',
		},
	];
};

const packagesData = (): JumpbarItemData[] => {
	return [
		{
			target: Routes[Routes.packages],
			value: App.text('packagesTitle'),
			title: App.text('packagesTitle'),
			icon: 'box-seam',
		},
	];
};

const pagesData = (): JumpbarItemData[] => {
	const data: JumpbarItemData[] = [];
	const pages: PageMetaData[] = Object.values(App.pages);

	pages.sort((a: KeyValueMap, b: KeyValueMap) =>
		a.mTime < b.mTime ? 1 : b.mTime < a.mTime ? -1 : 0
	);

	pages.forEach((page: PageMetaData) => {
		const icon = page.private ? 'eye-slash-fill' : 'file-earmark-text';

		data.push({
			target: `${Routes[Routes.page]}?url=${page.url}`,
			value: `${page.title} ${page.url}`,
			title: page.title,
			subtitle: page.url,
			icon,
		});
	});

	return data;
};

const jumpbarData = (): JumpbarItemData[] => {
	return [].concat(
		searchData(),
		inPageData(),
		settingsData(),
		sharedData(),
		packagesData(),
		pagesData()
	);
};

/**
 * The Jumpbar field element.
 *
 * @example
 * <am-jumpbar></am-jumpbar>
 *
 * @extends AutocompleteComponent
 */
class JumpbarComponent extends AutocompleteComponent {
	/**
	 * The autocomplete data.
	 */
	protected get data(): JumpbarItemData[] {
		return jumpbarData();
	}

	/**
	 * The initial index.
	 */
	protected initialIndex: null | number = 0;

	/**
	 * The minimum input value length to trigger the dropdown.
	 */
	protected minInputLength = 0;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		super.connectedCallback();

		this.listeners.push(
			keyCombo('j', () => {
				if (App.navigationIsLocked) {
					return;
				}

				if (this.input === document.activeElement) {
					this.input.blur();
					this.close();
				} else {
					this.input.focus();
				}
			})
		);
	}

	/**
	 * Create a dropdown item.
	 *
	 * @param item
	 * @returns the dropdown item element
	 */
	protected createItemElement(item: JumpbarItemData): HTMLElement {
		const attributes: KeyValueMap = {};

		['external', 'target'].forEach(
			(attribute: 'external' | 'target'): void => {
				if (item[attribute]) {
					attributes[attribute] = item[attribute];
				}
			}
		);

		const element = create('am-link', [classes.dropdownItem], attributes);

		element.innerHTML = this.itemHtml(item.icon, item.title, item.subtitle);

		return element;
	}

	/**
	 * Create a value for a dropdown item.
	 *
	 * @param item
	 * @returns the item value
	 */
	protected createItemValue(item: JumpbarItemData): string {
		return item.value.toLowerCase();
	}

	/**
	 * Create the inner HTML for a dropdown item.
	 *
	 * @param icon
	 * @param title
	 * @param subtitle
	 * @returns the HTML string
	 */
	private itemHtml(icon: string, title: string, subtitle: string): string {
		return html`
			<i class="bi bi-${icon}"></i>
			<span>$${title}</span>
			<span class="${classes.muted}">$${subtitle || ''}</span>
		`;
	}

	/**
	 * Update the dropdown and the list of filtered items based on the current input value.
	 */
	protected update(): void {
		const search = this.itemsFiltered[0];

		search.element.setAttribute(
			'href',
			`${search.item.url}&search=${encodeURIComponent(this.input.value)}`
		);

		search.value = `${search.value} ${this.input.value.toLowerCase()}`;
		search.element.innerHTML = this.itemHtml(
			search.item.icon,
			search.item.title,
			this.input.value
		);

		super.update();
	}

	/**
	 * Select an item and use the item value as the input value.
	 *
	 * @param event
	 */
	select(event: Event) {
		if (this.selectedIndex !== null) {
			this.itemsFiltered[this.selectedIndex].element.click();
		}

		this.close();
	}
}

customElements.define('am-jumpbar', JumpbarComponent);
