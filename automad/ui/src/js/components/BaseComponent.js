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

/**
 * The Automad base component. All Automad components are based on this class.
 *
 * @extends HTMLElement
 */
export class BaseComponent extends HTMLElement {
	/**
	 * Key/value pairs of the element attributes.
	 *
	 * @type {Object}
	 */
	elementAttributes = {};

	/**
	 * The class constructor.
	 */
	constructor() {
		super();
	}

	/**
	 * The array of observed attributes.
	 *
	 * @type {Array}
	 * @static
	 */
	static get observedAttributes() {
		return [];
	}

	/**
	 * The object with all classes used for HTML elements that are used by components.
	 *
	 * @type {Object}
	 */
	get cls() {
		return {
			button: 'am-e-button',
			buttonSuccess: 'am-e-button--success',
			hidden: 'am-u-display-none',
			overflowHidden: 'am-u-overflow-hidden',
			dropdownItem: 'am-c-dropdown__item',
			dropdownItemActive: 'am-c-dropdown__item--active',
			dropdown: 'am-c-dropdown',
			field: 'am-c-field',
			fieldChanged: 'am-c-field--changed',
			fieldLabel: 'am-c-field__label',
			input: 'am-e-input',
			inputLarge: 'am-e-input--large',
			inputTitle: 'am-e-input--title',
			muted: 'am-u-text-muted',
			modal: 'am-c-modal',
			modalOpen: 'am-c-modal--open',
			modalDialog: 'am-c-modal__dialog',
			modalHeader: 'am-c-modal__header',
			modalClose: 'am-c-modal__close',
			modalFooter: 'am-c-modal__footer',
			navItem: 'am-c-nav__item',
			navItemActive: 'am-c-nav__item--active',
			navLink: 'am-c-nav__link',
			navLinkHasChildren: 'am-c-nav__link--has-children',
			navChildren: 'am-c-nav__children',
			spinner: 'am-e-spinner',
			switcherLinkActive: 'am-c-switcher-link--active',
		};
	}

	/**
	 * The callback that is used when attributes are changed or on initialization.
	 *
	 * @param {string} name
	 * @param {string} oldValue
	 * @param {string} newValue
	 */
	attributeChangedCallback(name, oldValue, newValue) {
		this.elementAttributes[name] = newValue || '';
	}
}
