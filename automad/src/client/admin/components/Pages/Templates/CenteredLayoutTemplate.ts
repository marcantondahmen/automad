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

import { App, Attr, CSS, html } from '../../../core';
import { Partials } from '../../../types';

export const centered = ({ title, main }: Partials): string => {
	return html`
		<div class="am-l-centered">
			<div class="am-l-centered__navbar">
				<nav class="${CSS.navbar}">
					<span>
						<a href="${App.baseURL}" class="${CSS.navbarItem}">
							$${App.sitename} &mdash; $${title}
						</a>
					</span>
					<span>
						<a
							href="${App.baseURL}"
							class="${CSS.navbarItem}"
							${Attr.tooltip}="${App.text('close')}"
						>
							<i class="bi bi-x"></i>
						</a>
					</span>
				</nav>
			</div>
			<div class="am-l-centered__main">
				<div class="am-l-centered__content">${main}</div>
			</div>
		</div>
	`;
};
