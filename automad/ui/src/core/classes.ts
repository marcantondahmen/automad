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
	cardTitle: 'am-c-card__title',
	cardBody: 'am-c-card__body',
	cardFooter: 'am-c-card__footer',
	displayNone: 'am-u-display-none',
	dropdownItems: 'am-c-dropdown__items',
	dropdownItemsFullWidth: 'am-c-dropdown__items--full-width',
	dropdownItem: 'am-c-dropdown__item',
	dropdownItemActive: 'am-c-dropdown__item--active',
	dropdown: 'am-c-dropdown',
	dropdownOpen: 'am-c-dropdown--open',
	dropdownForm: 'am-c-dropdown--form',
	field: 'am-c-field',
	fieldChanged: 'am-c-field--changed',
	fieldLabel: 'am-c-field__label',
	flex: 'am-u-flex',
	flexItemGrow: 'am-u-flex__item-grow',
	grid: 'am-c-grid',
	input: 'am-e-input',
	inputTitle: 'am-e-input--title',
	muted: 'am-u-text-muted',
	modal: 'am-c-modal',
	modalOpen: 'am-c-modal--open',
	modalDialog: 'am-c-modal__dialog',
	modalDialogFullscreen: 'am-c-modal__dialog--fullscreen',
	modalHeader: 'am-c-modal__header',
	modalClose: 'am-c-modal__close',
	modalFooter: 'am-c-modal__footer',
	nav: 'am-c-nav',
	navItem: 'am-c-nav__item',
	navItemActive: 'am-c-nav__item--active',
	navLabel: 'am-c-nav__label',
	navLink: 'am-c-nav__link',
	navLinkHasChildren: 'am-c-nav__link--has-children',
	navChildren: 'am-c-nav__children',
	overflowHidden: 'am-u-overflow-hidden',
	root: 'am-c-root',
	rootLoading: 'am-c-root--loading',
	switcherLinkActive: 'am-c-switcher-link--active',
	upload: 'am-c-upload',
	uploadDropzone: 'am-c-upload__dropzone',
	uploadWindow: 'am-c-upload__window',
	uploadWindowOpen: 'am-c-upload__window--open',
	uploadWindowGrip: 'am-c-upload__window-grip',
	uploadPreviews: 'am-c-upload__previews',
};
