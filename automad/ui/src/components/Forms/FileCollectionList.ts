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

import { classes, create, fire, queryAll } from '../../core';
import { File, KeyValueMap } from '../../types';
import { FormComponent } from './Form';

export const FileCollectionUpdateEventName = 'AutomadFileCollectionUpdate';

/**
 * The file collection form component.
 *
 * @example
 * <am-file-collection-list api="FileCollection/list"></am-file-collection-list>
 * <am-submit form="FileCollection/list">Submit</am-submit>
 *
 * @extends FormComponent
 */
export class FileCollectionListComponent extends FormComponent {
	/**
	 * Enable self init.
	 */
	protected get initSelf(): boolean {
		return true;
	}

	/**
	 * Create the form after the response was received successfully.
	 *
	 * @param response - the response data
	 */
	protected processResponse(response: KeyValueMap): void {
		super.processResponse(response);

		if (typeof response.data == 'undefined') {
			return;
		}

		this.innerHTML = '';

		this.watch();

		if (typeof response.data.files == 'undefined') {
			return;
		}

		const grid = create('div', [classes.grid], {}, this);

		response.data.files.forEach((file: File[]) => {
			const card = create('am-file-card', [], {}, grid);

			card.data = file;
		});

		fire(FileCollectionUpdateEventName);
	}

	/**
	 * Reset the selection and submit the form in order to refresh the list of files.
	 */
	refresh(): void {
		queryAll('[type="checkbox"]', this).forEach(
			(checkbox: HTMLInputElement) => {
				checkbox.checked = false;
			}
		);

		this.hasUnsavedChanges = false;
		this.submit(true);
	}
}

customElements.define('am-file-collection-list', FileCollectionListComponent);
