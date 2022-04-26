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
	classes,
	create,
	createProgressModal,
	eventNames,
	fire,
	listen,
	notifyError,
	notifySuccess,
	requestAPI,
} from '../../core';
import { BaseComponent } from '../Base';

/**
 * The package list component.
 *
 * @extends BaseComponent
 */
class UpdateAllPackagesComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const button = create(
			'am-icon-text',
			[],
			{ icon: 'arrow-repeat', text: App.text('packagesUpdateAll') },
			this
		);

		listen(button, 'click', async () => {
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

			fire(eventNames.packagesChange);
		});
	}
}

customElements.define('am-update-all-packages', UpdateAllPackagesComponent);
