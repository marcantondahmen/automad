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
	const button = create(
		'span',
		[classes.button, classes.buttonInverted],
		{},
		container
	);

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
	 * @async
	 */
	private async render(pkg: Package): Promise<void> {
		this.classList.add(classes.card);
		this.classList.toggle(classes.cardActive, pkg.installed);

		const href = `${packageBrowser}/${pkg.name}`;
		const bodyTitle = create('div', [classes.cardBody], {}, this);
		const preview = create(
			'a',
			[
				classes.cardImage,
				classes.cardImage43,
				classes.cardImageIconLarge,
			],
			{ href, target: '_blank' },
			this
		);
		const description = create(
			'div',
			[classes.cardBody, classes.flexItemGrow],
			{},
			this
		);
		const footer = create('div', [classes.cardFooter], {}, this);

		const title = create('div', [classes.cardTitle], {}, bodyTitle);

		title.innerHTML = html`
			$${pkg.name.split('/')[0]} /
			<br />
			$${pkg.name.split('/')[1]}
		`;

		create('i', ['bi', 'bi-box-seam', classes.textMuted], {}, preview);

		description.textContent = pkg.description;

		footer.innerHTML = html`
			<div>
				<span class="am-e-badge">
					<span>↓ $${pkg.downloads}</span>
				</span>
				<a href="$${pkg.repository}" target="_blank">
					<i class="am-u-text-muted bi bi-github"></i>
				</a>
				<a href="${href}" target="_blank">
					<i class="am-u-text-muted bi bi-file-earmark-text-fill"></i>
				</a>
			</div>
		`;

		const buttons = create('small', [], {}, footer);

		if (pkg.outdated) {
			createUpdateButton(pkg, buttons);
		}

		if (pkg.installed) {
			createRemoveButton(pkg, buttons);
		} else {
			createInstallButton(pkg, buttons);
		}

		const thumbnail = await getThumbnail(pkg.repository);

		if (thumbnail) {
			preview.innerHTML = '';
			create('img', [], { src: thumbnail }, preview);
		}
	}
}

customElements.define('am-package-card', PackageCardComponent);
