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
	App,
	Attr,
	getTagFromRoute,
	html,
	isInvite,
	routes,
	UserController,
} from '@/admin/core';
import { BaseCenteredLayoutComponent } from './BaseCenteredLayout';

/**
 * The password reset code request view.
 *
 * @extends BaseCenteredLayoutComponent
 */
class RequestVerificationCodeComponent extends BaseCenteredLayoutComponent {
	/**
	 * Set the page title that is used a document title suffix.
	 */
	protected get pageTitle(): string {
		return isInvite()
			? App.text('completeAccountSetup')
			: App.text('accountRecovery');
	}

	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderMainPartial(): string {
		return html`
			<am-request-password-reset-code-form
				${Attr.api}="${UserController.requestPasswordResetCode}"
			>
			</am-request-password-reset-code-form>
		`;
	}
}

customElements.define(
	getTagFromRoute(routes.requestVerificationCode),
	RequestVerificationCodeComponent
);
