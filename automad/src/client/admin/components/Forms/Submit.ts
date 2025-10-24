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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { Attr, queryAll, queryParents } from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';
import { FormComponent } from './Form';

/**
 * A submit button element. Submit buttons are connected to a form by the "form" attribute.
 * The "form" attribute uses the api of the related form to connect.
 *
 * @example
 * <am-form ${Attr.api}="Class/method">
 *     <input name="title">
 * </am-form>
 * <am-submit ${Attr.form}="Class/method">Submit</am-submit>
 *
 * @extends BaseComponent
 */
export class SubmitComponent extends BaseComponent {
	/**
	 * The observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return [Attr.form];
	}

	/**
	 * The forms that are submitted by this button.
	 */
	get relatedForms(): Element[] {
		let forms = queryAll(
			`[${Attr.api}="${this.elementAttributes[Attr.form]}"]`
		);

		if (forms.length == 0) {
			forms = queryParents(`[${Attr.api}]`, this);
		}

		return forms;
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		const submit = () => {
			if (this.hasAttribute('disabled')) {
				return false;
			}

			this.relatedForms.forEach((form: FormComponent) => {
				form.submit();
			});
		};

		this.setAttribute('tabindex', '0');

		this.listen(this, 'click', submit.bind(this));
	}
}

customElements.define('am-submit', SubmitComponent);
