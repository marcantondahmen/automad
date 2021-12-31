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

import { debounce, listen, queryAll } from '../utils/core';
import { requestController } from '../utils/request';
import { BaseComponent } from './BaseComponent';

/**
 * Self initialized form with watched submit button and page URL:
 * <am-form controller="Class::method" init watch></am-form>
 *
 * Submit buttons are connected to a form by the "form" attribute.
 * The "form" attribute uses the controller of the related form to connect.
 * <am-form-submit form="Class::method">
 *     Submit
 * </am-form-submit>
 *
 * Normal form with watched button:
 * <am-form controller="Page::add" page="/url" watch>
 *     <input name="title">
 *     ...
 * </am-form>
 * <am-form-submit form="Page::add">
 *     Submit
 * </am-form-submit>
 */

class FormSubmit extends BaseComponent {
	static get observedAttributes() {
		return ['form'];
	}

	get relatedForms() {
		return queryAll(`[controller="${this.elementAttributes.form}"]`);
	}

	connectedCallback() {
		const submit = () => {
			if (this.hasAttribute('disabled')) {
				return false;
			}

			this.relatedForms.forEach((form) => {
				form.submit();
			});
		};

		listen(this, 'click', submit.bind(this));
	}
}

export class Form extends BaseComponent {
	static get observedAttributes() {
		return ['controller', 'page'];
	}

	get submitButtons() {
		return queryAll(
			`am-form-submit[form="${this.elementAttributes.controller}"]`
		);
	}

	get formData() {
		const data = {};

		if (this.elementAttributes.page) {
			data.url = this.elementAttributes.page;
		}

		queryAll('[name]', this).forEach((field) => {
			const name = field.getAttribute('name');
			const value = field.value;

			data[name] = value;
		});

		return data;
	}

	connectedCallback() {
		if (this.hasAttribute('init')) {
			this.submit();
		}

		if (this.hasAttribute('watch')) {
			this.watch();
		}
	}

	disbableButtons() {
		this.submitButtons.forEach((button) => {
			button.setAttribute('disabled', '');
		});
	}

	enableButtons() {
		this.submitButtons.forEach((button) => {
			button.removeAttribute('disabled');
		});
	}

	updateFieldStatus(element, changed) {
		element
			.closest(`.${this.cls.field}`)
			.classList.toggle(this.cls.fieldChanged, changed);
	}

	resetInputStatus() {
		queryAll(`.${this.cls.input}`, this).forEach((input) => {
			this.updateFieldStatus(input, false);
		});
	}

	async submit() {
		const response = await requestController(
			this.elementAttributes.controller,
			this.formData
		);

		this.disbableButtons();
		this.resetInputStatus();
		this.processResponse(response);
	}

	processResponse(response) {}

	watch() {
		const onChange = debounce((event) => {
			this.enableButtons();
			this.updateFieldStatus(event.target, true);
		}, 100);

		listen(
			this,
			'change keyup input',
			onChange.bind(this),
			'input, textarea, select'
		);

		setTimeout(this.disbableButtons.bind(this), 0);
	}
}

customElements.define('am-form-submit', FormSubmit);
customElements.define('am-form', Form);
