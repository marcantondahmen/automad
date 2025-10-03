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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
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
	notifyError,
	notifySuccess,
	PackageManagerController,
	requestAPI,
} from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';

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
		this.classList.add(CSS.displayNone, CSS.button);
		this.textContent = App.text('packagesUpdateAll');

		this.init();

		this.listen(
			window,
			EventName.packagesUpdateCheck,
			this.init.bind(this)
		);

		this.listen(this, 'click', async () => {
			const modal = createProgressModal(App.text('packagesUpdatingAll'));

			modal.open();

			const { error, success } = await requestAPI(
				PackageManagerController.updateAll
			);

			if (error) {
				notifyError(error);
			}

			if (success) {
				notifySuccess(success);
			}

			modal.close();

			fire(EventName.packagesChange);

			App.checkForOutdatedPackages();
		});
	}

	/**
	 * Check if the button should be displayed.
	 */
	private async init(): Promise<void> {
		const hasUpdates = App.state.outdatedPackages > 0;

		this.classList.toggle(CSS.displayNone, !hasUpdates);
	}
}

customElements.define('am-update-all-packages', UpdateAllPackagesComponent);
