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
 * The enum with all classes used for HTML elements that are used by components.
 */
export enum classes {
	alert = 'am-c-alert',
	alertDanger = 'am-c-alert--danger',
	alertPrimary = 'am-c-alert--primary',
	alertIcon = 'am-c-alert__icon',
	alertText = 'am-c-alert__text',
	badge = 'am-e-badge',
	breadcrumbs = 'am-c-breadcrumbs',
	breadcrumbsItem = 'am-c-breadcrumbs__item',
	button = 'am-e-button',
	buttonLink = 'am-e-button--link',
	buttonIcon = 'am-e-button--icon',
	buttonPrimary = 'am-e-button--primary',
	buttonPrimaryInverted = 'am-e-button--primary-inverted',
	buttonDanger = 'am-e-button--danger',
	buttonInverted = 'am-e-button--inverted',
	card = 'am-c-card',
	cardActive = 'am-c-card--active',
	cardLink = 'am-c-card--link',
	cardList = 'am-c-card--list',
	cardBadge = 'am-c-card__badge',
	cardImage = 'am-c-card__image',
	cardImageBlend = 'am-c-card__image--blend',
	cardImage43 = 'am-c-card__image--4-3',
	cardImageIconLarge = 'am-c-card__image--icon-large',
	cardFooter = 'am-c-card__footer',
	cardIcon = 'am-c-card__icon',
	cardIconButtons = 'am-c-card__icon-buttons',
	cardIconNarrow = 'am-c-card__icon--narrow',
	cardTitle = 'am-c-card__title',
	checkbox = 'am-f-checkbox',
	displayNone = 'am-u-display-none',
	dropdownItems = 'am-c-dropdown__items',
	dropdownItemsFullWidth = 'am-c-dropdown__items--full-width',
	dropdownItem = 'am-c-dropdown__item',
	dropdownItemDivider = 'am-c-dropdown__item--divider',
	dropdownItemActive = 'am-c-dropdown__item--active',
	dropdown = 'am-c-dropdown',
	dropdownRight = 'am-c-dropdown--right',
	dropdownOpen = 'am-c-dropdown--open',
	dropdownForm = 'am-c-dropdown--form',
	feedFieldSelect = 'am-c-feed-field-select',
	feedFieldSelectMuted = 'am-c-feed-field-select--muted',
	feedFieldSelectArrows = 'am-c-feed-field-select__arrows',
	feedFieldSelectItem = 'am-c-feed-field-select__item',
	feedFieldSelectItemGhost = 'am-c-feed-field-select__item--ghost',
	feedFieldSelectItemChosen = 'am-c-feed-field-select__item--chosen',
	feedFieldSelectItemDrag = 'am-c-feed-field-select__item--drag',
	field = 'am-c-field',
	fieldLabel = 'am-c-field__label',
	flex = 'am-u-flex',
	flexGap = 'am-u-flex--gap',
	flexAlignCenter = 'am-u-flex--align-center',
	flexColumn = 'am-u-flex--column',
	flexBetween = 'am-u-flex--between',
	flexItemGrow = 'am-u-flex__item-grow',
	grid = 'am-c-grid',
	iconText = 'am-c-icon-text',
	input = 'am-f-input',
	inputTitle = 'am-f-input--title',
	inputKeyCombo = 'am-f-input-key-combo',
	menu = 'am-c-menu',
	menuItem = 'am-c-menu__item',
	modal = 'am-c-modal',
	modalOpen = 'am-c-modal--open',
	modalDialog = 'am-c-modal__dialog',
	modalDialogFullscreen = 'am-c-modal__dialog--fullscreen',
	modalHeader = 'am-c-modal__header',
	modalClose = 'am-c-modal__close',
	modalFooter = 'am-c-modal__footer',
	nav = 'am-c-nav',
	navDragging = 'am-c-nav--dragging',
	navItem = 'am-c-nav__item',
	navItemGhost = 'am-c-nav__item--ghost',
	navItemChosen = 'am-c-nav__item--chosen',
	navItemDrag = 'am-c-nav__item--drag',
	navItemActive = 'am-c-nav__item--active',
	navGrip = 'am-c-nav__grip',
	navLabel = 'am-c-nav__label',
	navLink = 'am-c-nav__link',
	navLinkHasChildren = 'am-c-nav__link--has-children',
	navChildren = 'am-c-nav__children',
	overflowHidden = 'am-u-overflow-hidden',
	root = 'am-c-root',
	rootLoading = 'am-c-root--loading',
	select = 'am-f-select',
	spinner = 'am-c-spinner',
	switcherSection = 'am-c-switcher-section',
	switcherSectionFields = 'am-c-switcher-section--fields',
	switcherSectionActive = 'am-c-switcher-section--active',
	switcherLinkActive = 'am-c-switcher-link--active',
	textMuted = 'am-u-text-muted',
	textPrimary = 'am-u-text-primary',
	toggle = 'am-f-toggle',
	toggleLarge = 'am-f-toggle--large',
	toggleInput = 'am-f-toggle--input',
	toggleSelect = 'am-f-toggle--select',
	toggleOn = 'am-f-toggle--on',
	toggleOff = 'am-f-toggle--off',
	toggleDefaultOn = 'am-f-toggle--default-on',
	upload = 'am-c-upload',
	uploadDropzone = 'am-c-upload__dropzone',
	uploadPreviews = 'am-c-upload__previews',
}
