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
	Attr,
	create,
	createProgressModal,
	CSS,
	EventName,
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

	fire(EventName.packagesChange);
};

/**
 * Create an update button.
 *
 * @param pkg
 * @param container
 */
const createUpdateButton = (pkg: Package, container: HTMLElement): void => {
	const button = create('span', [], {}, container);

	create('span', [], {}, button).textContent = App.text('packageUpdate');

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
	const button = create('span', [], {}, container);

	create('span', [], {}, button).textContent = App.text('packageInstall');

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
	const button = create('span', [], {}, container);

	create('span', [], {}, button).textContent = App.text('packageRemove');

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
	const header = create('div', [CSS.cardHeader], {}, container);

	const title = html`
		$${pkg.name.split('/')[0]} /<br />$${pkg.name.split('/')[1]}
	`;

	header.innerHTML = html`
		<div class="${CSS.cardTitle}">${title}</div>
		<am-dropdown class="${CSS.cardHeaderDropdown}" ${Attr.right}>
			<i class="bi bi-three-dots"></i>
			<div class="${CSS.dropdownItems}">
				<a href="${href}" class="${CSS.dropdownLink}" target="_blank">
					<am-icon-text
						${Attr.icon}="file-richtext"
						${Attr.text}="Readme"
					></am-icon-text>
				</a>
				<a
					href="${pkg.repository}"
					class="${CSS.dropdownLink}"
					target="_blank"
				>
					<am-icon-text
						${Attr.icon}="git"
						${Attr.text}="Repository"
					></am-icon-text>
				</a>
			</div>
		</am-dropdown>
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
		[CSS.cardTeaser],
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
	create('div', [CSS.cardBody], {}, container).textContent = pkg.description;
};

/**
 * Create the card footer.
 *
 * @param pkg
 * @param container
 */
const createFooter = (pkg: Package, container: HTMLElement): void => {
	const buttons = create('div', [CSS.cardButtons], {}, container);

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
		this.classList.add(CSS.card);
		this.classList.toggle(CSS.cardActive, pkg.installed);

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
