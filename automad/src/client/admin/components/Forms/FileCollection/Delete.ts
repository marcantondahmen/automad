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

import { App, confirm } from '@/admin/core';
import { FormComponent } from '@/admin/components/Forms/Form';
import { BaseFileCollectionSubmitComponent } from './BaseSubmit';

/**
 * A delete button for the FileCollectionListComponent form.

 * @extends BaseFileCollectionSubmitComponent
 */
class FileCollectionDeleteComponent extends BaseFileCollectionSubmitComponent {
	/**
	 * The actual submit implementation for deleteing files.
	 */
	async submit(): Promise<void> {
		if (this.hasAttribute('disabled')) {
			return;
		}

		if (await confirm(App.text('confirmDeleteSelectedFiles'))) {
			this.relatedForms.forEach((form: FormComponent) => {
				form.additionalData = { action: 'delete' };
				form.submit();
			});
		}
	}
}

customElements.define(
	'am-file-collection-delete',
	FileCollectionDeleteComponent
);
