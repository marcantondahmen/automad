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

export class BaseComponent extends HTMLElement {
	elementAttributes = {};

	constructor() {
		super();
	}

	static get observedAttributes() {
		return [];
	}

	get cls() {
		return {
			hidden: 'am-u-display-none',
			overflowHidden: 'am-u-overflow-hidden',
			dropdownItem: 'am-c-dropdown__item',
			dropdownItemActive: 'am-c-dropdown__item--active',
			dropdown: 'am-c-dropdown',
			field: 'am-c-field',
			fieldChanged: 'am-c-field--changed',
			input: 'am-e-input',
			inputLarge: 'am-e-input--large',
			muted: 'am-u-text-muted',
			modalOpen: 'am-c-modal--open',
			sideNavItem: 'am-c-nav__item',
			sideNavItemActive: 'am-c-nav__item--active',
			sideNavLink: 'am-c-nav__link',
			sideNavLinkHasChildren: 'am-c-nav__link--has-children',
			sideNavChildren: 'am-c-nav__children',
			spinner: 'am-e-spinner',
		};
	}

	attributeChangedCallback(name, oldValue, newValue) {
		this.elementAttributes[name] = newValue || '';
	}
}
