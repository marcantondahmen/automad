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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import DOMPurify from 'dompurify';
import { KeyValueMap } from '@/admin/types';

/**
 * A whitelist of custom component attributes.
 */
export const enum Attr {
	api = 'am-api',
	auto = 'am-auto',
	badge = 'am-badge',
	bind = 'am-bind',
	bindTo = 'am-bind-to',
	binding = 'am-binding-name',
	clearForm = 'am-clear-form',
	confirm = 'am-confirm',
	data = 'am-data',
	destroy = 'am-destroy',
	enter = 'am-enter',
	event = 'am-event',
	error = 'am-error',
	external = 'am-external',
	file = 'am-file',
	focus = 'am-focus',
	form = 'am-form',
	hideCurrent = 'am-hide-current',
	icon = 'am-icon',
	key = 'am-key',
	label = 'am-label',
	loadingAnimation = 'am-loading-animation',
	min = 'am-min',
	modal = 'am-modal',
	modalOpen = 'am-modal-open',
	narrow = 'am-narrow',
	noClick = 'am-no-click',
	noEsc = 'am-no-esc',
	noFocus = 'am-no-focus',
	noTooltip = 'am-no-tooltip',
	page = 'am-page',
	path = 'am-path',
	publicationState = 'am-publication-state',
	portal = 'am-portal',
	right = 'am-right',
	section = 'am-section',
	target = 'am-target',
	text = 'am-text',
	toggle = 'am-toggle',
	tooltip = 'am-tooltip',
	tooltipOptions = 'am-tooltip-options',
	url = 'am-url',
	watch = 'am-watch',
	width = 'am-width',
}

/**
 * The DOMPurify options.
 *
 * @see {@link DOMPurify https://github.com/cure53/DOMPurify#can-i-configure-dompurify}
 */
const dompurifyOption = {
	CUSTOM_ELEMENT_HANDLING: {
		tagNameCheck: /^(am|sortable)-/,
		attributeNameCheck: /^am-/,
		allowCustomizedBuiltInElements: true,
	},
	ADD_ATTR: [
		Attr.bind,
		Attr.bindTo,
		Attr.publicationState,
		Attr.tooltip,
		Attr.tooltipOptions,
		Attr.toggle,
		'target',
	],
	ADD_URI_SAFE_ATTR: [Attr.tooltipOptions],
	SANITIZE_DOM: false,
};

/**
 * Handle the rendering of template literals and optionally escape values
 * that are preceeded with a `$`.
 *
 * @example
 * return html`
 *     <p>${ value }</p>
 *     <p>$${ escapedValue }</p>
 * `;
 *
 * @see {@link 2ality https://2ality.com/2015/01/template-strings-html.html#the-template-handler}
 * @see {@link DOMPurify https://github.com/cure53/DOMPurify}
 * @param strings
 * @param values
 * @returns the rendered template
 */
export const html = (strings: any, ...values: any[]): string => {
	let raw = strings.raw;

	let result = '';

	values.forEach((value, i) => {
		let section = raw[i];

		if (section.endsWith('$')) {
			value = htmlSpecialChars(value);
			section = section.slice(0, -1);
		}

		result += section + value;
	});

	result += raw[raw.length - 1];

	return DOMPurify.sanitize(result, dompurifyOption);
};

/**
 * Convert all HTML special characters.
 *
 * @param value
 * @returns the converted string
 */
export const htmlSpecialChars = (value: string | number): string => {
	const chars: KeyValueMap = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#039;',
		'/': '&#8203;/&#8203;',
	};

	return value.toString().replace(/[&<>"'\/]/g, (char: string) => {
		return chars[char];
	});
};
