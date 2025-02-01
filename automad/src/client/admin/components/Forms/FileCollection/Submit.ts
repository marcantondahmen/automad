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

import { EventName, listen, queryAll } from '@/admin/core';
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
			const handler = () => {
				this.toggleAttribute(
					'disabled',
					queryAll(':checked', form).length == 0
				);
			};

			this.addListener(
				listen(window, EventName.fileCollectionRender, handler)
			);

			this.addListener(listen(form, 'change', handler));
		});
	}
}

customElements.define(
	'am-file-collection-submit',
	FileCollectionSubmitComponent
);
