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
	Attr,
	create,
	CSS,
	EventName,
	fire,
	html,
	notifyError,
	PackageManagerController,
	requestAPI,
} from '@/admin/core';
import { KeyValueMap, Package } from '@/admin/types';
import { BaseComponent } from '@/admin/components/Base';
import { PackageCardComponent } from './PackageCard';

/**
 * Get and sort all required package data in order to generate the package card grid.
 *
 * @returns the list of package objects
 */
const getPackages = async (): Promise<Package[]> => {
	const { data, error } = await requestAPI(
		PackageManagerController.getPackageCollection
	);

	if (!data || error) {
		notifyError(error);

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
	 * The list container element.
	 */
	private listContainer: HTMLElement;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.innerHTML = html`
			<div class="${CSS.flex} ${CSS.flexGap}">
				<am-update-all-packages></am-update-all-packages>
				<am-filter
					placeholder="packagesFilter"
					${Attr.target}="${PackageCardComponent.TAG_NAME}"
				></am-filter>
			</div>
		`;

		this.listContainer = create(
			'div',
			[CSS.grid],
			{},
			this,
			'<am-spinner></am-spinner>'
		);

		this.init();

		this.listen(window, EventName.packagesChange, this.init.bind(this));
	}

	/**
	 * Init the component.
	 */
	private async init(): Promise<void> {
		const packages = await getPackages();

		this.listContainer.innerHTML = '';

		packages.forEach((pkg) => {
			const card = create('am-package-card', [], {}, this.listContainer);

			card.data = pkg;
		});

		fire(EventName.packagesRender);
	}
}

customElements.define('am-package-list', PackageListComponent);
