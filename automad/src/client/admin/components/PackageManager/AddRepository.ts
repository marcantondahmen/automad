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
	createField,
	createFormModal,
	CSS,
	EventName,
	FieldTag,
} from '@/admin/core';
import { create, PackageManagerController } from '@/common';

/**
 * The private packages component.
 *
 * @extends BaseComponent
 */
export class AddRepositoryComponent extends BaseComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 */
	static TAG_NAME = 'am-add-repository';

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const addRepositoryButton = create(
			'button',
			[CSS.button, CSS.buttonPrimary],
			{},
			this,
			App.text('repositoryAdd')
		);

		this.listen(addRepositoryButton, 'click', async () => {
			const { modal, form } = createFormModal(
				PackageManagerController.addRepository,
				EventName.repositoriesChange,
				App.text('repositoryAddTitle'),
				App.text('repositoryAddButton')
			);

			create('p', [], {}, form, App.text('repositoryAddInfo'));

			createField(FieldTag.platformSelect, form, {
				key: 'platform',
				value: 'github',
				name: 'platform',
				label: 'Platform Type',
			});

			createField(
				FieldTag.input,
				form,
				{
					key: 'name',
					value: '',
					name: 'name',
					placeholder: 'vendor/package',
				},
				[],
				{ required: '' }
			);

			createField(
				FieldTag.input,
				form,
				{
					key: 'repositoryUrl',
					value: '',
					name: 'repositoryUrl',
					label: 'Repository URL',
					placeholder: 'https://github.com/user/repository',
				},
				[],
				{ required: '' }
			);

			createField(
				FieldTag.input,
				form,
				{
					key: 'branch',
					value: '',
					name: 'branch',
					placeholder: 'main',
				},
				[],
				{ required: '' }
			);

			setTimeout(() => {
				modal.open();
			}, 0);
		});
	}
}

customElements.define(AddRepositoryComponent.TAG_NAME, AddRepositoryComponent);
