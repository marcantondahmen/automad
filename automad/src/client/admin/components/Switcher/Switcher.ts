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

import { BaseComponent } from '../Base';
import {
	Attr,
	EventName,
	fire,
	getSearchParam,
	listen,
	query,
	queryAll,
	setSearchParam,
} from '../../core';
import { SwitcherLinkComponent } from './SwitcherLink';
import { SwitcherLabelComponent } from './SwitcherLabel';
import { SwitcherSectionComponent } from './SwitcherSection';

export enum Section {
	overview = 'overview',
	cache = 'cache',
	users = 'users',
	update = 'update',
	feed = 'feed',
	language = 'language',
	debug = 'debug',
	config = 'config',
	settings = 'settings',
	text = 'text',
	colors = 'colors',
	files = 'files',
}

/**
 * Get the section name from the query string.
 *
 * @returns the section name that is used in the query string
 */
export const getActiveSection = (): string => {
	return getSearchParam('section');
};

/**
 * Update the URL and its query string to reflect the active section.
 *
 * @param section
 */
export const setActiveSection = (section: string): void => {
	setSearchParam('section', section);

	fire(EventName.switcherChange);
};

/**
 * A switcher menu component. A switcher can have any layout and is not more that a container with items.
 *
 * @example
 * <am-switcher>
 *     <am-switcher-link ${Attr.section}="text">Text</am-switcher-link>
 *     <am-switcher-link ${Attr.section}="settings">Settings</am-switcher-link>
 * </am-switcher>
 *
 * @see {@link SwitcherLinkComponent}
 * @see {@link SwitcherLabelComponent}
 * @see {@link SwitcherSectionComponent}
 * @extends BaseComponent
 */
export class SwitcherComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		setTimeout(() => {
			this.onChange();
			window.dispatchEvent(new Event(EventName.switcherChange));
		}, 0);

		this.listeners.push(
			listen(window, EventName.switcherChange, this.onChange.bind(this))
		);
	}

	/**
	 * The callback function triggered when the active item is changed.
	 */
	onChange(): void {
		let activeSection = getActiveSection();
		const sections: string[] = [];

		queryAll(SwitcherLinkComponent.TAG_NAME, this).forEach(
			(link: SwitcherLinkComponent) => {
				sections.push(link.getAttribute(Attr.section));
			}
		);

		if (sections.indexOf(activeSection) == -1) {
			setActiveSection(sections[0]);
		}

		query('html').scrollTop = 0;
	}
}

customElements.define('am-switcher', SwitcherComponent);
