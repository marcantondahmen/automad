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
		<div class="${CSS.layoutCentered}">
			<div class="${CSS.layoutCenteredNavbar}">
				<nav class="${CSS.navbar}">
					<a href="${App.baseURL}" class="${CSS.navbarItem}">
						<am-logo></am-logo>
					</a>
					<a
						href="${App.baseURL}"
						class="${CSS.navbarItem}"
						${Attr.tooltip}="${App.text('close')}"
					>
						<i class="bi bi-x"></i>
					</a>
				</nav>
			</div>
			<div class="${CSS.layoutCenteredMain}">
				<div class="${CSS.layoutCenteredContent}">${main}</div>
			</div>
		</div>
	`;
};
