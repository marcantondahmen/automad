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

import { BaseComponent } from '@/admin/components/Base';
import { App, Attr, CSS, html, Route, Section } from '../core';

/**
 * An alert component that is displayed whenever a user has no associated email address.
 *
 * @extends BaseComponent
 */
class MissingEmailAlertComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		if (App.user.email) {
			return;
		}

		this.classList.add(CSS.alert);

		this.innerHTML = html`
			<div class="${CSS.alertIcon}">
				<i class="bi bi-envelope-x"></i>
			</div>
			<div class="${CSS.alertText}">
				<div>${App.text('missingEmailAlert')}</div>
				<div>
					<am-link
						class="${CSS.button} ${CSS.buttonPrimary}"
						${Attr.target}="${Route.system}?section=${Section.users}"
					>
						${App.text('openUserSettings')}
					</am-link>
				</div>
			</div>
		`;
	}
}

customElements.define('am-missing-email-alert', MissingEmailAlertComponent);
