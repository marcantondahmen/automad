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

/**
 * The enum with all classes used for HTML elements that are used by components.
 */
export enum CSS {
	badge = 'am-e-badge',
	badgeMuted = 'am-e-badge--muted',

	breadcrumbs = 'am-c-breadcrumbs',
	breadcrumbsItem = 'am-c-breadcrumbs__item',

	button = 'am-e-button',
	buttonAccent = 'am-e-button--accent',
	buttonPrimary = 'am-e-button--primary',
	buttonLink = 'am-e-button--link',

	card = 'am-c-card',
	cardActive = 'am-c-card--active',
	cardTeaser = 'am-c-card__teaser',
	cardTitle = 'am-c-card__title',
	cardBody = 'am-c-card__body',
	cardBodyLarge = 'am-c-card__body--large',
	cardForm = 'am-c-card__form',
	cardFormButtons = 'am-c-card__form-buttons',
	cardFooter = 'am-c-card__footer',

	checkbox = 'am-f-checkbox',

	cursorPointer = 'am-u-cursor-pointer',

	displayNone = 'am-u-display-none',

	dropdown = 'am-c-dropdown',
	dropdownRight = 'am-c-dropdown--right',
	dropdownOpen = 'am-c-dropdown--open',
	dropdownContent = 'am-c-dropdown__content',
	dropdownDivider = 'am-c-dropdown__divider',
	dropdownItems = 'am-c-dropdown__items',
	dropdownLink = 'am-c-dropdown__link',
	dropdownLinkActive = 'am-c-dropdown__link--active',

	feedFieldSelect = 'am-c-feed-field-select',
	feedFieldSelectMuted = 'am-c-feed-field-select--muted',
	feedFieldSelectArrows = 'am-c-feed-field-select__arrows',
	feedFieldSelectItem = 'am-c-feed-field-select__item',
	feedFieldSelectItemGhost = 'am-c-feed-field-select__item--ghost',
	feedFieldSelectItemChosen = 'am-c-feed-field-select__item--chosen',
	feedFieldSelectItemDrag = 'am-c-feed-field-select__item--drag',

	field = 'am-c-field',
	fieldLabel = 'am-c-field__label',

	filter = 'am-c-filter',
	filterInput = 'am-c-filter__input',
	filterKeyCombo = 'am-c-filter__key-combo',

	flex = 'am-u-flex',
	flexAlignCenter = 'am-u-flex--align-center',
	flexColumn = 'am-u-flex--column',
	flexGap = 'am-u-flex--gap',
	flexBetween = 'am-u-flex--between',
	flexItemGrow = 'am-u-flex__item-grow',

	grid = 'am-l-grid',

	iconText = 'am-c-icon-text',

	input = 'am-f-input',
	inputTitle = 'am-f-input--title',
	inputCombo = 'am-f-input-combo',
	inputComboColor = 'am-f-input-combo__color',
	inputComboButton = 'am-f-input-combo__button',

	keyCombo = 'am-e-key-combo',

	menu = 'am-c-menu',
	menuItem = 'am-c-menu__item',
	menuItemActive = 'am-c-menu__item--active',

	modal = 'am-c-modal',
	modalOpen = 'am-c-modal--open',
	modalDialog = 'am-c-modal__dialog',
	modalDialogFullscreen = 'am-c-modal__dialog--fullscreen',
	modalHeader = 'am-c-modal__header',
	modalClose = 'am-c-modal__close',
	modalBody = 'am-c-modal__body',
	modalFooter = 'am-c-modal__footer',

	modalJumpbarInput = 'am-c-modal__jumpbar-input',
	modalJumpbarItems = 'am-c-modal__jumpbar-items',
	modalJumpbarLink = 'am-c-modal__jumpbar-link',
	modalJumpbarLinkActive = 'am-c-modal__jumpbar-link--active',
	modalJumpbarDivider = 'am-c-modal__jumpbar-divider',

	nav = 'am-c-nav',
	navSelectForm = 'am-c-nav--select',
	navDragging = 'am-c-nav--dragging',
	navChildren = 'am-c-nav__children',
	navGrip = 'am-c-nav__grip',
	navItem = 'am-c-nav__item',
	navItemActive = 'am-c-nav__item--active',
	navItemGhost = 'am-c-nav__item--ghost',
	navItemChosen = 'am-c-nav__item--chosen',
	navItemDrag = 'am-c-nav__item--drag',
	navLabel = 'am-c-nav__label',
	navLink = 'am-c-nav__link',
	navLinkHasChildren = 'am-c-nav__link--has-children',

	navbar = 'am-c-navbar',
	navbarItem = 'am-c-navbar__item',
	navbarGroup = 'am-c-navbar__group',

	overflowHidden = 'am-u-overflow-hidden',

	privacyIndicator = 'am-c-privacy-indicator',

	root = 'am-c-root',
	rootLoading = 'am-c-root--loading',

	select = 'am-f-select',

	spinner = 'am-c-spinner',

	switcherSection = 'am-c-switcher-section',
	switcherSectionActive = 'am-c-switcher-section--active',

	textMuted = 'am-u-text-muted',
	textMono = 'am-u-text-mono',

	toggle = 'am-f-toggle',
	toggleButton = 'am-f-toggle--button',
	toggleLarge = 'am-f-toggle--large',
	toggleSelect = 'am-f-toggle--select',
	toggleOn = 'am-f-toggle--on',
	toggleOff = 'am-f-toggle--off',
	toggleDefaultOn = 'am-f-toggle--default-on',

	upload = 'am-c-upload',
	uploadDropzone = 'am-c-upload__dropzone',
	uploadPreviews = 'am-c-upload__previews',
}
