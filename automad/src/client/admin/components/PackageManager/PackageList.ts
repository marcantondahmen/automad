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
 * Copyright (c) 2022-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	create,
	CSS,
	EventName,
	listen,
	PackageManagerController,
	requestAPI,
} from '@/admin/core';
import { KeyValueMap, Package } from '@/admin/types';
import { BaseComponent } from '@/admin/components/Base';

/**
 * Get and sort all required package data in order to generate the package card grid.
 *
 * @returns the list of package objects
 */
const getPackages = async (): Promise<Package[]> => {
	const { data } = await requestAPI(
		PackageManagerController.getPackageCollection
	);

	if (!data) {
		return [];
	}

	const packages = data.packages;

	packages.sort((a: KeyValueMap, b: KeyValueMap) =>
		a.installed < b.installed ? 1 : b.installed < a.installed ? -1 : 0
	);

	return packages;
};

/**
 * The package list component.
 *
 * @extends BaseComponent
 */
class PackageListComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.grid);
		this.init();

		this.addListener(
			listen(window, EventName.packagesChange, this.init.bind(this))
		);
	}

	/**
	 * Init the component.
	 */
	private async init(): Promise<void> {
		this.innerHTML = '<am-spinner></am-spinner>';

		try {
			const packages = await getPackages();

			this.innerHTML = '';

			packages.forEach((pkg) => {
				const card = create('am-package-card', [], {}, this);

				card.data = pkg;
			});
		} catch (error) {
			create(
				'am-alert',
				[],
				{
					icon: 'exclamation-circle',
					type: 'danger',
					text: 'packagistConnectionError',
				},
				this
			);
		}
	}
}

customElements.define('am-package-list', PackageListComponent);
