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
 * Copyright (c) 2022-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { App, Attr, CSS, html } from '@/admin/core';
import { Partials } from '@/admin/types';

export const centered = ({ main }: Partials): string => {
	return html`
		<div class="${CSS.layoutCentered}">
			<div class="${CSS.layoutCenteredNavbar}">
				<nav class="${CSS.navbar}">
					<a href="${App.baseIndex || '/'}" class="${CSS.navbarItem}">
						<am-logo></am-logo>
					</a>
					<a
						href="${App.baseIndex || '/'}"
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
