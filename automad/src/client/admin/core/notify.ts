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
import { query, create } from '.';
import { KeyValueMap } from '../types';

const defaults: Toastify.Options = {
	close: false,
	gravity: 'top',
	position: 'right',
	stopOnFocus: true,
	escapeMarkup: false,
	destination: null,
};

/**
 * Create and show a notification toast.
 *
 * @see {@link toastify https://github.com/apvarun/toastify-js}
 * @see {@link icons https://icons.getbootstrap.com}
 * @param params
 * @param params.message
 * @param params.icon
 * @param params.duration
 * @param params.className
 */
const notify = ({ message, icon, duration, className }: KeyValueMap) => {
	const ui = query('html.am-ui > body, body .am-ui');
	const node = create('div', ['am-c-notify__node'], {});

	create('i', ['am-c-notify__icon', 'bi', `bi-${icon}`], {}, node);
	create('span', ['am-c-notify__text'], {}, node).innerHTML = message;
	create('span', ['am-c-notify__close'], {}, node);

	className = `am-c-notify ${className}`;

	const toast: KeyValueMap = Toastify(
		Object.assign(defaults, {
			node,
			duration,
			className,
			selector: ui,
			onClick: function () {
				toast.hideToast();
			},
		})
	);

	toast.showToast();
};

/**
 * Show an error notification.
 *
 * @param message
 */
export const notifyError = (message: string): void => {
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
 * @param message
 */
export const notifySuccess = (message: string): void => {
	notify({
		message,
		duration: 3000,
		icon: 'check',
		className: '',
	});
};
