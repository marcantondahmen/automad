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

import {
	App,
	createProgressModal,
	CSS,
	EventName,
	fire,
	listen,
	notifyError,
	notifySuccess,
	requestAPI,
} from '../../core';
import { BaseComponent } from '../Base';

/**
 * The update all button component. It is only visible in case there are outdated packages.
 *
 * @extends BaseComponent
 */
class UpdateAllPackagesComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.displayNone, CSS.button, CSS.buttonPrimary);
		this.textContent = App.text('packagesUpdateAll');

		this.init();

		this.listeners.push(
			listen(window, EventName.packagesChange, this.init.bind(this))
		);

		listen(this, 'click', async () => {
			const modal = createProgressModal(App.text('packagesUpdatingAll'));

			modal.open();

			const { error, success } = await requestAPI(
				'PackageManager/updateAll'
			);

			if (error) {
				notifyError(error);
			}

			if (success) {
				notifySuccess(success);
			}

			modal.close();

			fire(EventName.packagesChange);
		});
	}

	/**
	 * Check if the button should be displayed.
	 */
	private async init(): Promise<void> {
		const { data } = await requestAPI('PackageManager/getOutdated');
		const hasUpdates = data?.outdated?.length > 0;

		this.classList.toggle(CSS.displayNone, !hasUpdates);
	}
}

customElements.define('am-update-all-packages', UpdateAllPackagesComponent);
