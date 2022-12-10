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

import { Attr, create, listen, query } from '.';
import Tooltip from 'codex-tooltip';
import { KeyValueMap } from '../types';

const getTooltipOptions = (element: HTMLElement): KeyValueMap => {
	const optionString = element.getAttribute(Attr.tooltipOptions) || '';
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
				if (element.matches(`[${Attr.tooltip}]`)) {
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

			let content = target.getAttribute(Attr.tooltip);

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
