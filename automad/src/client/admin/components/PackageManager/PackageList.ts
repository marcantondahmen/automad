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
	classes,
	create,
	eventNames,
	listen,
	request,
	requestAPI,
} from '../../core';
import { KeyValueMap, Package } from '../../types';
import { BaseComponent } from '../Base';

/**
 * Get the list of available packages.
 *
 * @returns the list of available packages
 */
const getPackagistPackages = async (): Promise<Package[]> => {
	const response = await request(
		'https://packagist.org/search.json?&type=automad-package'
	);
	const { results } = await response.json();

	return results;
};

/**
 * Get the outdated packages.
 *
 * @returns the object of outdated packages
 */
const getOutdatedPackages = async (): Promise<KeyValueMap> => {
	const { data } = await requestAPI('PackageManager/getOutdated');
	const packages: KeyValueMap = {};

	data?.outdated.forEach((pkg: Package) => {
		packages[pkg.name] = pkg;
	});

	return packages;
};

/**
 * Get all installed packages.
 *
 * @returns the object of installed packages
 */
const getInstalledPackages = async (): Promise<KeyValueMap> => {
	const { data } = await requestAPI('PackageManager/getInstalled');

	return data.installed;
};

/**
 * Get and sort all required package data in order to generate the package card grid.
 *
 * @returns the list of package objects
 */
const getPackages = async (): Promise<Package[]> => {
	const packages = await getPackagistPackages();
	const outdated = await getOutdatedPackages();
	const installed = await getInstalledPackages();

	packages.forEach((pkg) => {
		pkg.outdated = typeof outdated[pkg.name] !== 'undefined';
		pkg.installed = typeof installed[pkg.name] !== 'undefined';
	});

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
		this.classList.add(classes.grid);
		this.setAttribute('style', '--min: 19rem;');
		this.init();

		this.listeners.push(
			listen(window, eventNames.packagesChange, this.init.bind(this))
		);
	}

	/**
	 * Init the component.
	 */
	private async init(): Promise<void> {
		this.innerHTML = '';

		try {
			const packages = await getPackages();

			packages.forEach((pkg) => {
				const card = create('am-package-card', [], {}, this);

				card.data = pkg;
			});
		} catch (error) {
			console.error(error);

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
