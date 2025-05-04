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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { BaseComponent } from '@/admin/components/Base';
import {
	App,
	Attr,
	confirm,
	CSS,
	EventName,
	html,
	listen,
	notifySuccess,
	requestAPI,
} from '@/admin/core';
import { Repository } from '@/admin/types';
import { create, PackageManagerController } from '@/common';
import { ComposerAuthComponent } from './ComposerAuth';
import { AddRepositoryComponent } from './AddRepository';
import { RepositoryCardComponent } from './RepositoryCard';

/**
 * The private packages component.
 *
 * @extends BaseComponent
 */
class RepositoryListComponent extends BaseComponent {
	/**
	 * The list container.
	 */
	private listContainer: HTMLElement;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.init();
	}

	/**
	 * Init the component.
	 */
	private async init(): Promise<void> {
		const menu = create('div', [CSS.flex, CSS.flexGap], {}, this);

		create(AddRepositoryComponent.TAG_NAME, [], {}, menu);
		create(ComposerAuthComponent.TAG_NAME, [], {}, menu);

		create('p', [], {}, this, App.text('repositoriesInfo'));

		this.listContainer = create(
			'div',
			[CSS.grid],
			{ style: '--min: 20rem;' },
			this,
			'<am-spinner></am-spinner>'
		);

		this.renderList();

		this.addListener(
			listen(
				window,
				EventName.repositoriesChange,
				this.renderList.bind(this)
			)
		);
	}

	/**
	 * Render the repository list.
	 */
	private async renderList(): Promise<void> {
		const repos = ((
			await requestAPI(PackageManagerController.getRepositoryCollection)
		)?.data ?? []) as Repository[];

		this.listContainer.innerHTML = '';

		repos.forEach((data) => {
			const card = create(
				RepositoryCardComponent.TAG_NAME,
				[],
				{},
				this.listContainer
			);

			card.data = data;
		});
	}
}

customElements.define('am-repository-list', RepositoryListComponent);
