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
	const button = create(
		'span',
		[classes.button, classes.buttonPrimary, classes.flexItemGrow],
		{},
		container
	);

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
	const button = create(
		'span',
		[classes.button, classes.flexItemGrow],
		{},
		container
	);

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
		[classes.button, classes.flexItemGrow],
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
 * Create a card header with dropdown.
 *
 * @param pkg
 * @param href
 * @param container
 */
const createHeader = (
	pkg: Package,
	href: string,
	container: HTMLElement
): void => {
	const header = create(
		'span',
		[classes.flex, classes.flexGap],
		{},
		container
	);

	const title = create(
		'a',
		[classes.cardTitle, classes.flexItemGrow],
		{ href, target: '_blank' },
		header
	);

	title.innerHTML = html`
		$${pkg.name.split('/')[0]} ⁄
		<br />
		$${pkg.name.split('/')[1]}
	`;

	const dropdown = create('span', [classes.cardIconButtons], {}, header);
	dropdown.innerHTML = html`
		<span>
			<am-dropdown right>
				<i class="bi bi-three-dots"></i>
				<span class="${classes.dropdownItems}">
					<a
						href="${pkg.repository}"
						class="${classes.dropdownItem}"
						target="_blank"
					>
						<am-icon-text
							icon="git"
							text="Repository"
						></am-icon-text>
					</a>
					<a
						href="${pkg.url}"
						class="${classes.dropdownItem}"
						target="_blank"
					>
						<am-icon-text
							icon="box-seam"
							text="Packagist"
						></am-icon-text>
					</a>
					<a
						href="${href}"
						class="${classes.dropdownItem}"
						target="_blank"
					>
						<am-icon-text
							icon="file-text"
							text="Readme"
						></am-icon-text>
					</a>
				</span>
			</am-dropdown>
		</span>
	`;
};

/**
 * Create a preview container.
 *
 * @param href
 * @param container
 * @returns the preview element
 */
const createPreview = (href: string, container: HTMLElement): HTMLElement => {
	const preview = create(
		'a',
		[
			classes.cardImage,
			classes.cardImage43,
			classes.cardImageIconLarge,
			classes.cardImageBlend,
		],
		{ href, target: '_blank' },
		container
	);

	create('i', ['bi', 'bi-box-seam'], {}, preview);

	return preview;
};

/**
 * Create the description section.
 *
 * @param pkg
 * @param container
 */
const createDescription = (pkg: Package, container: HTMLElement): void => {
	const description = create(
		'div',
		[classes.flexItemGrow, classes.cardText],
		{},
		container
	);

	description.textContent = pkg.description;

	return description;
};

/**
 * Create the card footer.
 *
 * @param pkg
 * @param container
 */
const createFooter = (pkg: Package, container: HTMLElement): void => {
	const footer = create('span', [classes.cardFooter], {}, container);
	const buttons = create(
		'span',
		[classes.flex, classes.flexGap, classes.flexItemGrow],
		{},
		footer
	);

	if (pkg.installed) {
		createRemoveButton(pkg, buttons);

		if (pkg.outdated) {
			createUpdateButton(pkg, buttons);
		}
	} else {
		createInstallButton(pkg, buttons);
	}
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

		createHeader(pkg, href, this);

		const preview = createPreview(href, this);

		createDescription(pkg, this);
		createFooter(pkg, this);

		const thumbnail = await getThumbnail(pkg.repository);

		if (thumbnail) {
			preview.innerHTML = '';
			create('img', [], { src: thumbnail }, preview);
		}
	}
}

customElements.define('am-package-card', PackageCardComponent);
