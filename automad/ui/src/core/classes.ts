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

import { KeyValueMap } from '../types';

/**
 * The object with all classes used for HTML elements that are used by components.
 */
export const classes: KeyValueMap = {
	alert: 'am-c-alert',
	alertDanger: 'am-c-alert--danger',
	alertSuccess: 'am-c-alert--success',
	alertIcon: 'am-c-alert__icon',
	alertText: 'am-c-alert__text',
	breadcrumbs: 'am-c-breadcrumbs',
	breadcrumbsItem: 'am-c-breadcrumbs__item',
	button: 'am-e-button',
	buttonSuccess: 'am-e-button--success',
	card: 'am-c-card',
	cardImage: 'am-c-card__image',
	cardBody: 'am-c-card__body',
	cardFooter: 'am-c-card__footer',
	cardTitle: 'am-c-card__title',
	checkbox: 'am-f-checkbox',
	checkboxLarge: 'am-f-checkbox--large',
	checkboxInput: 'am-f-checkbox--input',
	checkboxSelect: 'am-f-checkbox--select',
	checkboxOn: 'am-f-checkbox--on',
	checkboxOff: 'am-f-checkbox--off',
	checkboxDefaultOn: 'am-f-checkbox--default-on',
	displayNone: 'am-u-display-none',
	dropdownItems: 'am-c-dropdown__items',
	dropdownItemsFullWidth: 'am-c-dropdown__items--full-width',
	dropdownItem: 'am-c-dropdown__item',
	dropdownItemActive: 'am-c-dropdown__item--active',
	dropdown: 'am-c-dropdown',
	dropdownOpen: 'am-c-dropdown--open',
	dropdownForm: 'am-c-dropdown--form',
	field: 'am-c-field',
	fieldLabel: 'am-c-field__label',
	flex: 'am-u-flex',
	flexColumn: 'am-u-flex--column',
	flexBetween: 'am-u-flex--between',
	flexItemGrow: 'am-u-flex__item-grow',
	grid: 'am-c-grid',
	iconText: 'am-c-icon-text',
	input: 'am-f-input',
	inputTitle: 'am-f-input--title',
	muted: 'am-u-text-muted',
	modal: 'am-c-modal',
	modalOpen: 'am-c-modal--open',
	modalDialog: 'am-c-modal__dialog',
	modalDialogFullscreen: 'am-c-modal__dialog--fullscreen',
	modalHeader: 'am-c-modal__header',
	modalClose: 'am-c-modal__close',
	modalFooter: 'am-c-modal__footer',
	nav: 'am-c-nav',
	navDragging: 'am-c-nav--dragging',
	navItem: 'am-c-nav__item',
	navItemGhost: 'am-c-nav__item--ghost',
	navItemChosen: 'am-c-nav__item--chosen',
	navItemDrag: 'am-c-nav__item--drag',
	navItemActive: 'am-c-nav__item--active',
	navGrip: 'am-c-nav__grip',
	navLabel: 'am-c-nav__label',
	navLink: 'am-c-nav__link',
	navLinkHasChildren: 'am-c-nav__link--has-children',
	navChildren: 'am-c-nav__children',
	overflowHidden: 'am-u-overflow-hidden',
	root: 'am-c-root',
	rootLoading: 'am-c-root--loading',
	switcherSection: 'am-c-switcher-section',
	switcherSectionFields: 'am-c-switcher-section--fields',
	switcherSectionActive: 'am-c-switcher-section--active',
	switcherLinkActive: 'am-c-switcher-link--active',
	upload: 'am-c-upload',
	uploadDropzone: 'am-c-upload__dropzone',
	uploadPreviews: 'am-c-upload__previews',
};
