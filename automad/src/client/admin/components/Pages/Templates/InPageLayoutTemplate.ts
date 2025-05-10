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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, CSS, html, InPageController } from '@/admin/core';
import { Partials } from '@/admin/types';

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
						<span class="${CSS.navbarGroup}">
							<am-undo-buttons
								class="${CSS.displaySmallNone}"
							></am-undo-buttons>
							<am-submit
								class="${CSS.button}"
								${Attr.form}="${InPageController.edit}"
								disabled
							>
								${App.text('save')}
							</am-submit>
							<am-link
								href="${App.baseIndex || '/'}"
								class="${CSS.navbarItem}"
								${Attr.bind}="inPageReturnUrl"
								${Attr.bindTo}="${Attr.external}"
							>
								<i class="bi bi-x"></i>
							</am-link>
						</span>
					</span>
				</nav>
			</div>
			<div class="${CSS.layoutInPageMain}">
				<div class="${CSS.layoutInPageContent}">${main}</div>
			</div>
		</div>
	`;
};
