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

import { BaseComponent } from '@/admin/components/Base';
import {
	App,
	Attr,
	create,
	CSS,
	getComponentTargetContainer,
	html,
	query,
	Route,
	Section,
} from '../core';
import { ModalComponent } from './Modal/Modal';

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

		const username = App.user.name
			.replace(/[^\w\-\_]+/g, '-')
			.toLowerCase();

		const key = `missing-email-notified-${username}`;

		if (localStorage.getItem(key)) {
			return;
		}

		const modal = create(
			ModalComponent.TAG_NAME,
			[],
			{ [Attr.noEsc]: '', [Attr.noClick]: '', [Attr.destroy]: '' },
			getComponentTargetContainer(),
			html`
				<am-modal-dialog>
					<am-modal-header>
						${App.text('missingEmailAlertTitle')}
					</am-modal-header>
					<am-modal-body>
						${App.text('missingEmailAlertBody')}
					</am-modal-body>
					<am-modal-footer>
						<am-modal-close class="${CSS.button}">
							${App.text('missingEmailAlertIgnore')}
						</am-modal-close>
						<a
							class="${CSS.button} ${CSS.buttonPrimary}"
							href="${App.dashboardURL}/${Route.system}?section=${Section.users}"
						>
							${App.text('missingEmailAlertOpenSettings')}
						</a>
					</am-modal-footer>
				</am-modal-dialog>
			`
		) as ModalComponent;

		this.listen(
			query('am-modal-footer am-modal-close', modal),
			'click',
			() => {
				localStorage.setItem(key, 'ignore');
			}
		);

		modal.open();
	}
}

customElements.define('am-missing-email-alert', MissingEmailAlertComponent);
