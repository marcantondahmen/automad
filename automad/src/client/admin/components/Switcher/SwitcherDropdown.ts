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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { Attr, create, CSS, EventName, html, listen } from '@/admin/core';
import { SwitcherDropdownData, SwitcherDropdownItem } from '@/admin/types';
import { BaseComponent } from '@/admin/components/Base';
import { getActiveSection } from './Switcher';
import { Section } from '@/common';

/**
 * The system menu switcher wrapper.
 *
 * @extends BaseComponent
 */
class SwitcherDropdownComponent extends BaseComponent {
	/**
	 * Initialze the menu by passing the menu data.
	 */
	set data(data: SwitcherDropdownData) {
		this.init(data);
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(
			CSS.layoutDashboardSection,
			CSS.layoutDashboardSectionSticky
		);

		const toggle = () => {
			this.classList.toggle(
				CSS.displayNone,
				getActiveSection() == Section.overview
			);
		};

		this.addListener(
			listen(window, EventName.switcherChange, toggle.bind(this))
		);

		toggle();
	}

	/**
	 * Init the component.
	 */
	private init(data: SwitcherDropdownData): void {
		const wrapper = create(
			'div',
			[
				CSS.layoutDashboardContent,
				CSS.layoutDashboardContentRow,
				...(this.hasAttribute(Attr.narrow)
					? [CSS.layoutDashboardContentNarrow]
					: []),
			],
			{},
			this
		);

		const switcher = create(
			'am-switcher',
			[CSS.formGroup, CSS.flexItemGrow],
			{},
			wrapper
		);

		const overview = create(
			'am-switcher-link',
			[CSS.formGroupItem, CSS.button, CSS.buttonIcon],
			{ [Attr.section]: data.overview.section },
			switcher
		);

		overview.innerHTML = `<i class="bi bi-${data.overview.icon}"></i>`;

		const dropdown = create(
			'am-dropdown',
			[CSS.flexItemGrow, CSS.flex],
			{},
			switcher
		);

		create(
			'am-switcher-label',
			[
				CSS.formGroupItem,
				CSS.flexItemGrow,
				CSS.formGroupItem,
				CSS.select,
			],
			{},
			dropdown
		);

		const items = create('div', [CSS.dropdownItems], {}, dropdown);

		data.items.forEach((item: SwitcherDropdownItem) => {
			items.innerHTML += html`
				<am-switcher-link
					class="${CSS.dropdownLink}"
					${Attr.section}="${item.section}"
				>
					${item.title}
				</am-switcher-link>
			`;
		});
	}
}

customElements.define('am-switcher-dropdown', SwitcherDropdownComponent);
