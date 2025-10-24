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

import { EventName, queryAll } from '@/admin/core';
import { FormComponent } from '@/admin/components/Forms/Form';
import { SubmitComponent } from '@/admin/components/Forms/Submit';

/**
 * The base class for a FileCollectionListComponent form submit button.

 * @extends SubmitComponent
 */
export abstract class BaseFileCollectionSubmitComponent extends SubmitComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		this.setAttribute('tabindex', '0');
		this.toggleAttribute('disabled', true);

		this.relatedForms.forEach((form: FormComponent) => {
			const handler = () => {
				this.toggleAttribute(
					'disabled',
					queryAll(':checked', form).length == 0
				);
			};

			this.listen(window, EventName.fileCollectionRender, handler);
			this.listen(form, 'change', handler);
		});

		this.listen(this, 'click', this.submit.bind(this));
	}

	/**
	 * The submit function.
	 */
	abstract submit(): void;
}
