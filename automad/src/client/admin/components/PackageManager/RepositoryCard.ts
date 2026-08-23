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
 * Copyright (c) 2025-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import {
	App,
	Attr,
	createFormModal,
	createProgressModal,
	CSS,
	dateFormat,
	EventName,
	fire,
	html,
	notifyError,
	notifySuccess,
	requestAPI,
} from '@/admin/core';
import { create, PackageManagerController } from '@/common';
import { BaseComponent } from '../Base';
import type { Repository } from './types';

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
				<a
					href="${data.repositoryUrl}"
					class="${CSS.cardTitle}"
					target="_blank"
				>
					${data.name}
				</a>
				<div class="${CSS.cardBody} ${CSS.flexGap}">
					<a
						href="${data.repositoryUrl}"
						class="${CSS.flex} ${CSS.flexColumn} ${CSS.textParagraph}"
						target="_blank"
					>
						<am-icon-text
							${Attr.icon}="chat-left"
							${Attr.text}="${data.description}"
						></am-icon-text>
						<am-icon-text
							${Attr.icon}="box-seam"
							${Attr.text}="${data.repositoryUrl}"
						></am-icon-text>
						<am-icon-text
							${Attr.icon}="tag"
							${Attr.text}="${data.branch}"
						></am-icon-text>
					</a>
					${!!data.commit
						? html`
								<div>
									<a
										href="${data.commit?.url}"
										class="${CSS.badge} ${CSS.badgeMuted} ${CSS.flexGap}"
										target="_blank"
										${Attr.tooltip}="${encodeURIComponent(
											data.commit?.message
										)}"
										${Attr.tooltipOptions}="placement:right"
									>
										<i class="bi bi-record-circle"></i>
										<span class="${CSS.textMono}">
											${data.commit?.hash.slice(0, 8)}
										</span>
										&mdash;
										<span
											>${dateFormat(
												data.commit?.timestamp
											)}</span
										>
									</a>
								</div>
							`
						: ''}
				</div>
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

		this.listen(remove, 'click', async () => {
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

		this.listen(update, 'click', async () => {
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
