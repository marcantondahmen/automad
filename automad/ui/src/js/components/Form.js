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

import { debounce, listen, query, queryAll } from '../utils/core';
import { notifyError } from '../utils/notify';
import { requestController } from '../utils/request';
import { BaseComponent } from './BaseComponent';

/**
 * A submit button element. Submit buttons are connected to a form by the "form" attribute.
 * The "form" attribute uses the controller of the related form to connect.
 * ```
 * <am-form controller="Class::method" page="/url" watch>
 *     <input name="title">
 * </am-form>
 * <am-form-submit form="Class::method">Submit</am-form-submit>
 * ```
 *
 * @extends BaseComponent
 */
class FormSubmit extends BaseComponent {
	/**
	 * The observed attributes.
	 *
	 * @type {Array}
	 * @static
	 */
	static get observedAttributes() {
		return ['form'];
	}

	/**
	 * The forms that are submitted by this button.
	 *
	 * @type {Array}
	 */
	get relatedForms() {
		return queryAll(`[controller="${this.elementAttributes.form}"]`);
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
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

/**
 * A basic form.
 *
 * The following options are available and can be passed as attributes:
 * - `controller` (required)
 * - `page`
 * - `init`
 * - `watch`
 * - `focus`
 *
 * Self initialized form with watched submit button and page URL:
 * ```
 * <am-form controller="Class::method" init watch></am-form>
 * ```
 * Focus the first input of a for when being connected:
 * ```
 * <am-form controller="Class::method" focus>
 *     <input>
 * </am-form>
 * ```
 *
 * @extends BaseComponent
 */
export class Form extends BaseComponent {
	/**
	 * The observed attributes.
	 *
	 * @type {Array}
	 * @static
	 */
	static get observedAttributes() {
		return ['controller', 'page'];
	}

	/**
	 * All related submit buttons.
	 *
	 * @type {Array}
	 */
	get submitButtons() {
		return queryAll(
			`am-form-submit[form="${this.elementAttributes.controller}"]`
		);
	}

	/**
	 * The form data object.
	 *
	 * @type {Object}
	 */
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

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		if (this.hasAttribute('init')) {
			this.submit();
		}

		if (this.hasAttribute('watch')) {
			this.watch();
		}

		if (this.hasAttribute('focus')) {
			setTimeout(() => {
				query('input').focus();
			}, 0);
		}
	}

	/**
	 * Disable all connected submit buttons.
	 */
	disbableButtons() {
		this.submitButtons.forEach((button) => {
			button.setAttribute('disabled', '');
		});
	}

	/**
	 * Enable all connected submit buttons.
	 */
	enableButtons() {
		this.submitButtons.forEach((button) => {
			button.removeAttribute('disabled');
		});
	}

	/**
	 * Update the class of the containing field to be marked as changed.
	 *
	 * @param {HTMLElement} element - The input element that has changed
	 * @param {boolean} changed
	 */
	updateFieldStatus(element, changed) {
		const field = element.closest(`.${this.cls.field}`);

		if (field) {
			field.classList.toggle(this.cls.fieldChanged, changed);
		}
	}

	/**
	 * Reset all fields to be marked as unchanged.
	 */
	resetFieldStatus() {
		queryAll(`.${this.cls.input}`, this).forEach((input) => {
			this.updateFieldStatus(input, false);
		});
	}

	/**
	 * Submit the form.
	 */
	async submit() {
		const response = await requestController(
			this.elementAttributes.controller,
			this.formData
		);

		if (this.hasAttribute('watch')) {
			this.disbableButtons();
		}

		this.resetFieldStatus();
		this.processResponse(response);
	}

	/**
	 * Process the response that is received after submitting the form.
	 *
	 * @param {Object} response
	 */
	processResponse(response) {
		if (response.error) {
			notifyError(response.error);
		}

		if (response.redirect) {
			window.location.href = data.redirect;
		}

		if (response.reload) {
			window.location.reload();
		}
	}

	/**
	 * Watch the form for changes.
	 */
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
