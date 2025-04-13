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

import { create, CSS, EventName, fire, listen, queryAll } from '@/admin/core';
import { File, KeyValueMap } from '@/admin/types';
import { FormComponent } from '@/admin/components/Forms/Form';

/**
 * The file collection form component.
 *
 * @example
 * <am-file-collection-move ${Attr.form}="FileCollectionController::list">Move Selected</am-file-collection-move>
 * <am-file-collection-delete ${Attr.form}="FileCollectionController::list">Delete Selected</am-file-collection-delete>
 * <am-file-collection-list-form ${Attr.api}="FileCollectionController::list"></am-file-collection-list-form>
 *
 * @extends FormComponent
 */
export class FileCollectionListFormComponent extends FormComponent {
	/**
	 * Enable self init.
	 */
	protected get initSelf(): boolean {
		return true;
	}

	/**
	 * Initialize the form.
	 */
	protected init(): void {
		super.init();

		this.classList.add(CSS.grid);
		this.setAttribute('style', '--min: 11.5rem; --aspect: 1.25;');

		this.addListener(
			listen(
				window,
				`${EventName.appStateChange} ${EventName.filesChangeOnServer}`,
				this.refresh.bind(this)
			)
		);
	}

	/**
	 * Create the form after the response was received successfully.
	 *
	 * @param response - the response data
	 * @async
	 */
	protected async processResponse(response: KeyValueMap): Promise<void> {
		await super.processResponse(response);

		if (typeof response.data == 'undefined') {
			return;
		}

		this.innerHTML = '';

		if (typeof response.data.files == 'undefined') {
			return;
		}

		response.data.files.forEach((file: File[]) => {
			const card = create('am-file-card', [], {}, this);

			card.data = file;
		});

		fire(EventName.fileCollectionRender);
	}

	/**
	 * Reset the selection and submit the form in order to refresh the list of files.
	 */
	refresh(): void {
		queryAll<HTMLInputElement>('[type="checkbox"]', this).forEach(
			(checkbox) => {
				checkbox.checked = false;
			}
		);

		this.submit(true);
	}
}

customElements.define(
	'am-file-collection-list-form',
	FileCollectionListFormComponent
);
