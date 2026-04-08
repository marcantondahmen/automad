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

import { App, Attr, CSS, EventName, html } from '@/admin/core';
import { FormComponent } from './Form';

/**
 * Render the list of registered users.
 *
 * @returns the rendered list
 */
const renderRegisteredUsers = (): string => {
	return App.system.users.reduce((output, user) => {
		const checkbox =
			App.user.name == user.name
				? html`
						<span class="${CSS.textMuted}">
							${App.text('systemUsersYou')}
						</span>
					`
				: html`<am-checkbox name="delete[${user.name}]"></am-checkbox>`;

		const totpBadge = user.totpIsConfigured
			? html`
					<i
						class="bi bi-shield-check"
						${Attr.tooltip}="${App.text(
							'systemUsersTotpIsEnabled'
						)}"
					></i>
				`
			: '';

		return html`
			${output}
			<span class="${CSS.card}">
				<span class="${CSS.cardBody} ${CSS.cardBodyLarge}">
					<span
						class="${CSS.flex} ${CSS.flexGap} ${CSS.flexAlignCenter}"
					>
						<span class="${CSS.flexItemGrow}">
							${user.name} (${user.email})
						</span>
						<span
							class="${CSS.flex} ${CSS.flexGap} ${CSS.flexAlignCenter}"
						>
							${totpBadge}${checkbox}
						</span>
					</span>
				</span>
			</span>
		`;
	}, '');
};

/**
 * The delete users form.
 *
 * @extends FormComponent
 */
export class DeleteUsersFormComponent extends FormComponent {
	/**
	 * Initialize the form.
	 */
	protected init(): void {
		const render = () => {
			this.innerHTML = renderRegisteredUsers();
		};

		this.classList.add(CSS.flex, CSS.flexColumn, CSS.flexGap);

		this.listen(window, EventName.appStateChange, render.bind(this));

		render();
	}
}

customElements.define('am-delete-users-form', DeleteUsersFormComponent);
