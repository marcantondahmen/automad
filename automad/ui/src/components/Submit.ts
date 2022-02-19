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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { listen, queryAll, queryParents } from '../core';
import { BaseComponent } from './Base';
import { FormComponent } from './Forms/Form';
import { ModalComponent } from './Modal/Modal';

/**
 * A submit button element. Submit buttons are connected to a form by the "form" attribute.
 * The "form" attribute uses the api of the related form to connect.
 *
 * @example
 * <am-form api="Class/method" watch>
 *     <input name="title">
 * </am-form>
 * <am-submit form="Class/method">Submit</am-submit>
 *
 * @extends BaseComponent
 */
class SubmitComponent extends BaseComponent {
	/**
	 * The observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['form'];
	}

	/**
	 * The forms that are submitted by this button.
	 */
	get relatedForms(): Element[] {
		let forms = queryAll(`[api="${this.elementAttributes.form}"]`);

		if (forms.length == 0) {
			forms = queryParents('[api]', this);
		}

		return forms;
	}

	/**
	 * Get the parent modal if existing.
	 */
	get parentModal(): ModalComponent {
		const modal = this.closest('am-modal') as ModalComponent;

		return modal || null;
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

			const modal = this.parentModal;

			if (modal) {
				modal.close();
			}
		};

		listen(this, 'click', submit.bind(this));
	}
}

customElements.define('am-submit', SubmitComponent);
