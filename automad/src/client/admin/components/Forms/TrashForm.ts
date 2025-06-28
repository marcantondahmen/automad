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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	create,
	CSS,
	html,
	PageTrashController,
	requestAPI,
} from '@/admin/core';
import { DeletedPageMetaData } from '@/admin/types';
import { BaseComponent } from '@/admin/components/Base';

/**
 * Create a card for a deleted page.
 *
 * @param page
 * @param container
 */
const createCard = (
	page: DeletedPageMetaData,
	container: HTMLElement
): void => {
	const modified = new Date(page.lastModified);

	create(
		'div',
		[CSS.card],
		{},
		container,
		html`
			<div class="${CSS.cardHeader}">
				<div>
					<am-icon-text
						class="${CSS.cardTitle}"
						${Attr.icon}="file-earmark-text"
						${Attr.text}="${page.title}"
					></am-icon-text>
					<div class="${CSS.cardBody}">
						${modified.toLocaleString()}
					</div>
				</div>
				<div>
					<am-dropdown
						class="${CSS.cardHeaderDropdown}"
						${Attr.right}
					>
						<i class="bi bi-three-dots"></i>
						<div class="${CSS.dropdownItems}">
							<am-form
								${Attr.api}="${PageTrashController.restore}"
							>
								<input
									type="hidden"
									name="path"
									value="${page.path}"
								/>
								<am-submit class="${CSS.dropdownLink}">
									<i class="bi bi-arrow-counterclockwise"></i>
									<span>${App.text('trashRestore')}</span>
								</am-submit>
							</am-form>
							<am-form
								${Attr.api}="${PageTrashController.permanentlyDelete}"
								${Attr.confirm}="${App.text(
									'trashPermanentlyDeleteConfirm'
								)}"
							>
								<input
									type="hidden"
									name="path"
									value="${page.path}"
								/>
								<am-submit class="${CSS.dropdownLink}">
									<i class="bi bi-x-lg"></i>
									<span>
										${App.text('trashPermanentlyDelete')}
									</span>
								</am-submit>
							</am-form>
						</div>
					</am-dropdown>
				</div>
			</div>
		`
	);
};

/**
 * The trash form.
 *
 * @extends BaseComponent
 */
export class TrashFormComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.init();
	}

	/**
	 * Fetch data and init the component.
	 */
	async init(): Promise<void> {
		this.innerHTML = '';

		const { data } = await requestAPI(PageTrashController.list);
		const container = create(
			'div',
			[CSS.flex, CSS.flexColumn, CSS.flexGap],
			{},
			this
		);

		if (!data.length) {
			create(
				'am-alert',
				[],
				{
					[Attr.icon]: 'slash-circle',
					[Attr.text]: App.text('trashIsEmpty'),
				},
				container
			);

			return;
		}

		data.forEach((page: DeletedPageMetaData) => {
			createCard(page, container);
		});
	}
}

customElements.define('am-trash-form', TrashFormComponent);
