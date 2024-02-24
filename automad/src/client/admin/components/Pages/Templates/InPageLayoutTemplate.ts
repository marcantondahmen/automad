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
 * Copyright (c) 2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, CSS, html, InPageController } from '@/core';
import { Partials } from '@/types';

export const inPage = ({ main }: Partials): string => {
	return html`
		<div class="${CSS.layoutInPage}">
			<div class="${CSS.layoutInPageNavbar}">
				<nav class="${CSS.navbar}">
					<span class="${CSS.navbarGroup} ${CSS.flexGap}">
						<a
							class="${CSS.navbarItem}"
							${Attr.bind}="inPageContextUrl"
							${Attr.bindTo}="href"
						>
							<span ${Attr.bind}="inPageTitle"></span>
						</a>
						<span
							class="${CSS.badge}"
							${Attr.bind}="inPageField"
						></span>
					</span>
					<span
						class="${CSS.flex} ${CSS.flexGap} ${CSS.flexAlignCenter}"
					>
						<am-undo-buttons></am-undo-buttons>
						<span class="${CSS.navbarItem}">
							<am-submit
								class="${CSS.button}"
								${Attr.form}="${InPageController.edit}"
								disabled
							>
								${App.text('save')}
							</am-submit>
						</span>
						<am-link
							href="${App.baseURL || '/'}"
							class="${CSS.navbarItem}"
							${Attr.bind}="inPageReturnUrl"
							${Attr.bindTo}="${Attr.external}"
						>
							<i class="bi bi-x"></i>
						</am-link>
					</span>
				</nav>
			</div>
			<div class="${CSS.layoutInPageMain}">
				<div class="${CSS.layoutInPageContent}">${main}</div>
			</div>
		</div>
	`;
};
