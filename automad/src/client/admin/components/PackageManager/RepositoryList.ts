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
	confirm,
	CSS,
	EventName,
	listen,
	notifySuccess,
	requestAPI,
} from '@/admin/core';
import { Repository } from '@/admin/types';
import { create, PackageManagerController } from '@/common';
import { ComposerAuthModalComponent } from './ComposerAuthModal';
import { AddRepositoryModalComponent } from './AddRepositoryModal';
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
		create(AddRepositoryModalComponent.TAG_NAME, [], {}, this);
		create(ComposerAuthModalComponent.TAG_NAME, [], {}, this);
		this.renderResetAuthModal();

		this.listContainer = create(
			'div',
			[CSS.grid],
			{ style: '--min: 24rem;' },
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

	/**
	 * Render the auth reset button and modal.
	 */
	private renderResetAuthModal(): void {
		const resetAuthButton = create(
			'button',
			[CSS.button],
			{},
			this,
			App.text('composerAuthReset')
		);
		listen(resetAuthButton, 'click', async () => {
			if (!(await confirm(App.text('composerAuthResetConfirm')))) {
				return;
			}

			const { success } = await requestAPI(
				PackageManagerController.resetAuth,
				{},
				true
			);

			if (success) {
				notifySuccess(success);
			}
		});
	}
}

customElements.define('am-repository-list', RepositoryListComponent);
