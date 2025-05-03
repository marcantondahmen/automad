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

import {
	App,
	Attr,
	createFormModal,
	createProgressModal,
	CSS,
	EventName,
	fire,
	html,
	listen,
	notifyError,
	notifySuccess,
	requestAPI,
} from '@/admin/core';
import { Repository } from '@/admin/types';
import { create, PackageManagerController } from '@/common';
import { BaseComponent } from '../Base';

/**
 * The package repository card component.
 *
 * @extends BaseComponent
 */
export class RepositoryCardComponent extends BaseComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 */
	static TAG_NAME = 'am-repository-card';

	/**
	 * Set the card data.
	 *
	 * @param data
	 */
	set data(data: Repository) {
		this.render(data);
	}

	/**
	 * Render the card.
	 *
	 * @param data
	 */
	private render(data: Repository): void {
		this.classList.add(CSS.card);

		create(
			'div',
			[CSS.flexItemGrow],
			{},
			this,
			html`
				<span class="${CSS.cardIcon}">
					<i class="bi bi-${data.platform}"></i>
				</span>
				<div class=${CSS.cardTitle}>${data.name}</div>
				<a
					href="${data.repositoryUrl}"
					class="${CSS.cardBody}"
					target="_blank"
				>
					<am-icon-text
						${Attr.icon}="chat-left"
						${Attr.text}="${data.description}"
					></am-icon-text>
					<am-icon-text
						${Attr.icon}="box-seam"
						${Attr.text}="${data.repositoryUrl}"
						title="${data.repositoryUrl}"
					></am-icon-text>
					<am-icon-text
						${Attr.icon}="tag"
						${Attr.text}="${data.branch}"
					></am-icon-text>
				</a>
			`
		);

		const buttons = create('div', [CSS.cardButtons], {}, this);
		const remove = create(
			'button',
			[],
			{},
			buttons,
			html`<span>${App.text('repositoryRemove')}</span>`
		);

		listen(remove, 'click', async () => {
			const { modal, form } = createFormModal(
				PackageManagerController.removeRepository,
				EventName.repositoriesChange,
				'',
				App.text('repositoryRemove')
			);

			form.textContent = App.text('repositoryRemoveConfirm');
			form.additionalData = { name: data.name };

			setTimeout(() => {
				modal.open();
			}, 0);
		});

		const update = create(
			'button',
			[],
			{},
			buttons,
			html`<span>${App.text('repositoryUpdate')}</span>`
		);

		listen(update, 'click', async () => {
			const progress = createProgressModal(
				App.text('repositoryUpdating')
			);

			progress.open();

			const { error, success } = await requestAPI(
				PackageManagerController.updateRepository,
				{ name: data.name }
			);

			progress.close();

			fire(EventName.repositoriesChange);

			if (error) {
				notifyError(error);
			}

			if (success) {
				notifySuccess(success);
			}
		});
	}
}

customElements.define(
	RepositoryCardComponent.TAG_NAME,
	RepositoryCardComponent
);
