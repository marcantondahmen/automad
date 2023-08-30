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
 * Copyright (c) 2021-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { AutocompleteComponent } from '@/components/Autocomplete';
import { JumpbarItemData, KeyValueMap, PageMetaData } from '@/types';
import { App, Attr, create, CSS, html, Route } from '@/core';
import { Section } from '@/components/Switcher/Switcher';

/**
 * Return the jumpbar autocompletion data for the search.
 *
 * @returns the jumpbar autocompletion data for the search
 */
const searchData = (): JumpbarItemData[] => {
	return [
		{
			target: Route.search,
			value: App.text('searchTitle'),
			title: App.text('searchTitle'),
			icon: 'search',
			cls: [CSS.modalJumpbarDivider],
		},
	];
};

/**
 * Return the jumpbar autocompletion data for the in page edit mode.
 *
 * @returns the jumpbar autocompletion data array
 */
const inPageData = (): JumpbarItemData[] => {
	return [
		{
			external: App.baseURL,
			value: App.text('inPageEdit'),
			title: App.text('inPageEdit'),
			icon: 'window-desktop',
			cls: [CSS.modalJumpbarDivider],
		},
	];
};

/**
 * Return the jumpbar autocompletion data for the system settings.
 *
 * @returns the jumpbar autocompletion data array
 */
const settingsData = (): JumpbarItemData[] => {
	const item = (
		section: string,
		title: string,
		icon: string,
		cls: string[] = []
	): JumpbarItemData => {
		return {
			target: `${Route.system}?section=${section}`,
			value: `${App.text('systemTitle')} ${App.text(title)}`,
			title: App.text(title),
			subtitle: App.text('systemTitle'),
			icon,
			cls,
		};
	};

	const data: JumpbarItemData[] = [
		item(Section.cache, 'systemCache', 'device-ssd'),
		item(Section.users, 'systemUsers', 'person-badge'),
		item(Section.update, 'systemUpdate', 'arrow-repeat'),
		item(Section.feed, 'systemRssFeed', 'rss'),
		item(Section.language, 'systemLanguage', 'translate'),
		item(Section.debug, 'systemDebug', 'bug'),
		item(Section.config, 'systemConfigFile', 'file-earmark-code', [
			CSS.modalJumpbarDivider,
		]),
	];

	return data;
};

/**
 * Return the jumpbar autocompletion data for the shared data.
 *
 * @returns the jumpbar autocompletion data array
 */
const sharedData = (): JumpbarItemData[] => {
	return [
		{
			target: Route.shared,
			value: App.text('sharedTitle'),
			title: App.text('sharedTitle'),
			icon: 'file-earmark-medical',
			cls: [CSS.modalJumpbarDivider],
		},
	];
};

/**
 * Return the jumpbar autocompletion data for the packages page.
 *
 * @returns the jumpbar autocompletion data array
 */
const packagesData = (): JumpbarItemData[] => {
	return [
		{
			target: Route.packages,
			value: App.text('packagesTitle'),
			title: App.text('packagesTitle'),
			icon: 'box-seam',
			cls: [CSS.modalJumpbarDivider],
		},
	];
};

/**
 * Return the jumpbar autocompletion data for the trash page.
 *
 * @returns the jumpbar autocompletion data array
 */
const trashData = (): JumpbarItemData[] => {
	return [
		{
			target: Route.trash,
			value: App.text('trashTitle'),
			title: App.text('trashTitle'),
			icon: 'trash3',
			cls: [CSS.modalJumpbarDivider],
		},
	];
};

/**
 * Return the jumpbar autocompletion data for pages, sorted by modification date.
 *
 * @returns the jumpbar autocompletion data array
 */
const pagesData = (): JumpbarItemData[] => {
	const data: JumpbarItemData[] = [];
	const pages: PageMetaData[] = Object.values(App.pages);

	pages.sort((a: KeyValueMap, b: KeyValueMap) =>
		a.lastModified < b.lastModified
			? 1
			: b.lastModified < a.lastModified
			? -1
			: 0
	);

	pages.forEach((page: PageMetaData) => {
		const icon = page.private ? 'eye-slash-fill' : 'file-earmark-text';

		data.push({
			target: `${Route.page}?url=${page.url}`,
			value: `${page.title} ${page.url}`,
			title: page.title,
			subtitle: page.url,
			icon,
		});
	});

	return data;
};

/**
 * Return the entire concatenated jumpbar autocompletion data.
 *
 * @returns the entire concatenated jumpbar autocompletion data array
 */
const jumpbarData = (): JumpbarItemData[] => {
	return [].concat(
		searchData(),
		inPageData(),
		settingsData(),
		sharedData(),
		packagesData(),
		trashData(),
		pagesData()
	);
};

/**
 * The jumpbar dialog component.
 *
 * @extends AutocompleteComponent
 */
class ModalJumpbarDialogComponent extends AutocompleteComponent {
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
	 * The dropdown items CSS class.
	 */
	protected elementClasses = [CSS.modalDialog];

	/**
	 * The dropdown input CSS class.
	 */
	protected inputClasses = [CSS.modalJumpbarInput];

	/**
	 * The dropdown items CSS class.
	 */
	protected itemsClasses = [CSS.modalJumpbarItems];

	/**
	 * The link class.
	 */
	protected linkClass = CSS.modalJumpbarLink;

	/**
	 * The active link class.
	 */
	protected linkActiveClass = CSS.active;

	/**
	 * Create a dropdown item.
	 *
	 * @param item
	 * @returns the dropdown item element
	 */
	protected createItemElement(item: JumpbarItemData): HTMLElement {
		const attributes: KeyValueMap = {};

		if (item.external) {
			attributes[Attr.external] = item.external;
		}

		if (item.target) {
			attributes[Attr.target] = item.target;
		}

		const cls = item.cls || [];
		const element = create(
			'am-link',
			cls.concat(CSS.modalJumpbarLink),
			attributes
		);

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
			<span class="${CSS.textMuted}">$${subtitle || ''}</span>
		`;
	}

	/**
	 * Update the dropdown and the list of filtered items based on the current input value.
	 */
	protected update(): void {
		const search = this.itemsFiltered[0];

		search.element.setAttribute(
			Attr.target,
			`${Route.search}?search=${encodeURIComponent(
				this.input.value
			).replace(/%20/g, '+')}`
		);

		search.value = `${search.value} ${this.input.value.toLowerCase()}`;
		search.element.innerHTML = this.itemHtml(
			search.item.icon,
			search.item.title,
			this.input.value
		);

		this.maxItems = Math.floor((window.innerHeight - 150) / 46);

		super.update();
	}

	/**
	 * Close the dropdown.
	 */
	close(): void {
		this.selectedIndex = this.initialIndex;
		this.toggleActiveItemStyle();
	}

	/**
	 * Open the dropdown.
	 */
	open(): void {}

	/**
	 * Select an item and use the item value as the input value.
	 *
	 * @param event
	 */
	select(event: Event) {
		event.preventDefault();

		if (this.selectedIndex !== null) {
			this.itemsFiltered[this.selectedIndex].element.click();
		}

		this.close();
	}
}

customElements.define('am-modal-jumpbar-dialog', ModalJumpbarDialogComponent);
