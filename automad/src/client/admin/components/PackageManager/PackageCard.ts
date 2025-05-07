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
	PackageManagerController,
	requestAPI,
} from '@/admin/core';
import { Package } from '@/admin/types';
import { BaseComponent } from '@/admin/components/Base';

const packageBrowser = 'https://packages.automad.org';

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

	App.checkForOutdatedPackages();
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
			PackageManagerController.update,
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
			PackageManagerController.install,
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

	if (pkg.isDependency) {
		button.setAttribute('disabled', '');
		button.setAttribute('title', App.text('packageIsDependency'));

		return;
	}

	listen(button, 'click', () => {
		performAction(
			pkg,
			PackageManagerController.remove,
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
	create(
		'div',
		[CSS.cardHeader],
		{},
		container,
		html`
			<div class="${CSS.cardTitle}">
				$${pkg.name.split('/')[0]} /<br />$${pkg.name.split('/')[1]}
			</div>
			<am-dropdown class="${CSS.cardHeaderDropdown}" ${Attr.right}>
				<i class="bi bi-three-dots"></i>
				<div class="${CSS.dropdownItems}">
					<a
						href="${href}"
						class="${CSS.dropdownLink}"
						target="_blank"
					>
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
		`
	);
};

/**
 * Create a preview container.
 *
 * @param pkg
 * @param href
 * @param container
 * @returns the preview element
 */
const createPreview = (
	pkg: Package,
	href: string,
	container: HTMLElement
): void => {
	const renderIcon = (container: HTMLElement) => {
		create('i', ['bi', 'bi-box-seam'], {}, container);
	};

	const link = create(
		'a',
		[CSS.cardTeaser],
		{ href, target: '_blank' },
		container
	);

	if (pkg.version) {
		const badgeCls = [CSS.badge];
		const badgeText = [pkg.version];

		if (pkg.outdated) {
			badgeText.push('');
			badgeText.push(pkg.latest);
		}

		create('span', badgeCls, {}, link, badgeText.join(' '));
	}

	if (pkg.image) {
		const img = create('img', [], { src: pkg.image }, link);

		listen(img, 'error', () => {
			img.remove();

			renderIcon(link);
		});

		return;
	}

	renderIcon(link);
};

/**
 * Create the description section.
 *
 * @param pkg
 * @param container
 */
const createDescription = (pkg: Package, container: HTMLElement): void => {
	create(
		'span',
		[CSS.textLimitRows],
		{},
		create('div', [CSS.cardBody], {}, container)
	).textContent = pkg.description;
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
		createPreview(pkg, href, this);
		createDescription(pkg, this);
		createFooter(pkg, this);
	}
}

customElements.define('am-package-card', PackageCardComponent);
