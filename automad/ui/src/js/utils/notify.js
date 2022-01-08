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

import Toastify from 'toastify-js';
import { query } from './core';
import { create } from './create';

const options = {
	close: true,
	duration: 3000,
	gravity: 'top',
	position: 'right',
	stopOnFocus: true,
	className: 'info',
	escapeMarkup: false,
	destination: false,
	close: false,
};

/**
 * Create and show a notification toast.
 *
 * @see {@link toastify https://github.com/apvarun/toastify-js}
 * @see {@link icons https://icons.getbootstrap.com}
 * @param {Objects} params
 * @param {string} params.message
 * @param {string} params.icon
 * @param {number} params.duration
 * @param {string} params.className
 */
const notify = ({ message, icon, duration, className }) => {
	const ui = query('html.am-ui > body, body .am-ui');
	const node = create('div', ['am-c-notify__node'], {});

	create('i', ['am-c-notify__icon', 'bi', `bi-${icon}`], {}, node);
	create('span', ['am-c-notify__text'], {}, node).innerHTML = message;
	create('span', ['am-c-notify__close'], {}, node);

	className = `am-c-notify ${className}`;

	const toast = Toastify(
		Object.assign(options, {
			node,
			duration,
			className,
			selector: ui,
			onClick: function () {
				toast.hideToast();
			},
		})
	).showToast();
};

/**
 * Show an error notification.
 *
 * @param {string} message
 */
export const notifyError = (message) => {
	notify({
		message,
		duration: -1,
		icon: 'exclamation-triangle',
		className: 'am-c-notify--error',
	});
};

/**
 * Show a success notification.
 *
 * @param {string} message
 */
export const notifySuccess = (message) => {
	notify({
		message,
		duration: 3000,
		icon: 'check',
		className: '',
	});
};
