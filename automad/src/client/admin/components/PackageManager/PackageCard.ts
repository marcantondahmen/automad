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
	html,
	listen,
	notifyError,
	notifySuccess,
	requestAPI,
} from '../../core';
import { Package } from '../../types';
import { BaseComponent } from '../Base';
import { ModalComponent } from '../Modal/Modal';

const packageBrowser = 'https://packages.automad.org';

/**
 * Get the tumbnail for a package.
 *
 * @param repository
 * @returns the thumbnail URL
 */
const getThumbnail = async (repository: string): Promise<string> => {
	const response = await requestAPI('PackageManager/getThumbnail', {
		repository,
	});

	return response.data?.thumbnail;
};

/**
 * Perform a package manager action.
 *
 * @param pkg
 * @param api
 * @param text
 */
const performAction = async (
	pkg: Package,
	api: string,
	text: string
): Promise<void> => {
	const modal = createProgressModal(text);

	modal.open();

	const { error, success } = await requestAPI(api, { package: pkg.name });

	if (error) {
		notifyError(error);
	}

	if (success) {
		notifySuccess(success);
	}

	modal.close();

	fire(eventNames.packagesChange);
};

/**
 * Create an update button.
 *
 * @param pkg
 * @param container
 */
const createUpdateButton = (pkg: Package, container: HTMLElement): void => {
	const button = create('span', [classes.button], {}, container);

	button.textContent = App.text('packageUpdate');

	listen(button, 'click', () => {
		performAction(
			pkg,
			'PackageManager/update',
			App.text('packageUpdating')
		);
	});
};

/**
 * Create an install button.
 *
 * @param pkg
 * @param container
 */
const createInstallButton = (pkg: Package, container: HTMLElement): void => {
	const button = create('span', [classes.button], {}, container);

	button.textContent = App.text('packageInstall');

	listen(button, 'click', () => {
		performAction(
			pkg,
			'PackageManager/install',
			App.text('packageInstalling')
		);
	});
};

/**
 * Create an remove button.
 *
 * @param pkg
 * @param container
 */
const createRemoveButton = (pkg: Package, container: HTMLElement): void => {
	const button = create('span', [classes.button], {}, container);

	button.textContent = App.text('packageRemove');

	listen(button, 'click', () => {
		performAction(
			pkg,
			'PackageManager/remove',
			App.text('packageRemoving')
		);
	});
};

/**
 * The package card component.
 *
 * @extends BaseComponent
 */
class PackageCardComponent extends BaseComponent {
	/**
	 * Render the package card as soon as the package data is provided.
	 */
	set data(pkg: Package) {
		this.render(pkg);
	}

	/**
	 * Render the card content for the provided package.
	 *
	 * @param pkg
	 */
	private async render(pkg: Package): Promise<void> {
		this.classList.add(classes.card);
		this.classList.toggle(classes.cardActive, pkg.installed);

		const href = `${packageBrowser}/${pkg.name}`;
		const preview = create(
			'a',
			[classes.cardImage],
			{ href, target: '_blank' },
			this
		);
		const body = create('div', [classes.cardBody], {}, this);
		const footer = create('div', [classes.cardFooter], {}, this);

		const title = create('div', [classes.cardTitle], {}, body);
		const description = create('span', [], {}, body);

		title.textContent = pkg.name;
		description.textContent = pkg.description;

		const button = create(
			'a',
			[classes.button],
			{ href, target: '_blank' },
			footer
		);

		button.textContent = 'Readme';

		const thumbnail = await getThumbnail(pkg.repository);

		if (thumbnail) {
			create('img', [], { src: thumbnail }, preview);
		}

		if (pkg.outdated) {
			createUpdateButton(pkg, footer);
		}

		if (pkg.installed) {
			createRemoveButton(pkg, footer);
		} else {
			createInstallButton(pkg, footer);
		}
	}
}

customElements.define('am-package-card', PackageCardComponent);
