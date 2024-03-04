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
 * Copyright (c) 2022-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { listen, queryAll } from '@/admin/core';
import { FormComponent } from '@/admin/components/Forms/Form';
import { SubmitComponent } from '@/admin/components/Forms/Submit';

/**
 * A submit button for the FileCollectionListComponent form.

 * @extends SubmitComponent
 */
class FileCollectionSubmitComponent extends SubmitComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		super.connectedCallback();

		this.toggleAttribute('disabled', true);

		this.relatedForms.forEach((form: FormComponent) => {
			listen(
				form,
				'change',
				() => {
					this.toggleAttribute(
						'disabled',
						queryAll(':checked', form).length == 0
					);
				},
				'[type="checkbox"]'
			);
		});
	}
}

customElements.define(
	'am-file-collection-submit',
	FileCollectionSubmitComponent
);
