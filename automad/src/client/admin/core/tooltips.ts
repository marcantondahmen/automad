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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { listen } from './events';
import { query } from './utils';
import Tooltip from 'codex-tooltip';
import { create } from './create';
import { KeyValueMap } from '../types';

enum tooltipAttr {
	content = 'am-tooltip',
	options = 'am-tooltip-options',
}

const getTooltipOptions = (element: HTMLElement): KeyValueMap => {
	const optionString = element.getAttribute(tooltipAttr.options) || '';
	const options: KeyValueMap = {};

	if (optionString) {
		const pairs = optionString.split(',');

		pairs.forEach((pair) => {
			const [key, value] = pair.split(':');

			if (isNaN(parseInt(value))) {
				options[key.trim()] = value.trim();
			} else {
				options[key.trim()] = parseInt(value);
			}
		});
	}

	return options;
};

/**
 * Init tooltips.
 *
 * @see {@link github https://github.com/codex-team/codex.tooltips}
 */
export const initTooltips = () => {
	const ui = query('.am-ui');
	const tooltip = new Tooltip();

	listen(ui, 'mouseover', (event: MouseEvent) => {
		const path = event.path || (event.composedPath && event.composedPath());
		let target: HTMLElement = null;

		path.forEach((element: any) => {
			try {
				if (element.matches(`[${tooltipAttr.content}]`)) {
					target = element;
				}
			} catch (error) {}
		});

		if (target) {
			const options = Object.assign(
				{
					placement: 'bottom',
					delay: 150,
					hidingDelay: 100,
				},
				getTooltipOptions(target)
			);

			let content = target.getAttribute(tooltipAttr.content);

			if (content) {
				const node = create('span');

				node.innerHTML = content;
				tooltip.show(target, node, options);
			}

			return;
		}

		tooltip.hide();
	});
};
