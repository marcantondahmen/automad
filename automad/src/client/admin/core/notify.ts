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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import Toastify from 'toastify-js';
import { query, create, CSS } from '.';
import { NotifyOptions } from '@/admin/types';

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
 * @param [params.className]
 */
const notify = ({ message, icon, duration, className }: NotifyOptions) => {
	const ui = query('html.am-ui > body, body .am-ui');
	const node = create('div', [CSS.notifyNode], {});

	create('i', [CSS.notifyIcon, 'bi', `bi-${icon}`], {}, node);
	create('span', [CSS.notifyText], {}, node).innerHTML = message;
	create('span', [CSS.notifyClose], {}, node);

	const toast = Toastify(
		Object.assign(defaults, {
			node,
			duration,
			className: className ?? CSS.notify,
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
		icon: 'x-circle',
		className: `${CSS.notify} ${CSS.notifyDanger}`,
	});
};

/**
 * Show a success notification.
 *
 * @param message
 * @param [duration]
 */
export const notifySuccess = (
	message: string,
	duration: number = 3000
): void => {
	notify({
		message,
		duration,
		icon: 'check-circle',
	});
};
