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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import {
	AiProviderController,
	App,
	Attr,
	CSS,
	html,
	requestAPI,
} from '@/admin/core';
import { BaseStateIndicatorComponent } from './BaseStateIndicator';

/**
 * The base AI validation indicator.
 *
 * @extends BaseUpdateIndicatorComponent
 */
export abstract class BaseAiValidationIndicator extends BaseStateIndicatorComponent {
	/**
	 * The validation endpoint.
	 */
	abstract getController(): AiProviderController;

	/**
	 * Render the state element.
	 */
	render(): void {
		const id = this.getAttribute(Attr.aiProviderId);

		const validate = async () => {
			const { data } = await requestAPI(this.getController(), { id });

			const cls = data?.isValid
				? `bi bi-check-circle-fill`
				: `bi bi-slash-circle-fill ${CSS.textDanger}`;

			this.innerHTML = html`<i class="${cls} ${CSS.iconFixedWidth}"></i>`;
		};

		this.innerHTML = html`<i
			class="bi bi-dash-circle ${CSS.iconFixedWidth} ${CSS.textMuted}"
		></i>`;

		if (App.system.ai.enabled) {
			setTimeout(validate, 2000);
		}
	}
}
