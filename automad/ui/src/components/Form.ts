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

import { classes, debounce, listen, query, queryAll } from '../utils/core';
import { notifyError } from '../utils/notify';
import { requestController } from '../utils/request';
import { InputElement, KeyValueMap } from '../utils/types';
import { BaseComponent } from './Base';

/**
 * A submit button element. Submit buttons are connected to a form by the "form" attribute.
 * The "form" attribute uses the controller of the related form to connect.
 *
 * @example
 * <am-form controller="Class::method" page="/url" watch>
 *     <input name="title">
 * </am-form>
 * <am-form-submit form="Class::method">Submit</am-form-submit>
 *
 * @extends BaseComponent
 */
class FormSubmitComponent extends BaseComponent {
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

			this.relatedForms.forEach((form: FormComponent) => {
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
 *
 * @example
 * <am-form controller="Class::method" init watch></am-form>
 *
 * Focus the first input of a for when being connected:
 *
 * @example
 * <am-form controller="Class::method" focus>
 *     <input>
 * </am-form>
 *
 * @extends BaseComponent
 */
export class FormComponent extends BaseComponent {
	/**
	 * The observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['controller', 'page'];
	}

	/**
	 * All related submit buttons.
	 */
	protected get submitButtons(): Element[] {
		return queryAll(
			`am-form-submit[form="${this.elementAttributes.controller}"]`
		);
	}

	/**
	 * The form data object.
	 */
	get formData(): KeyValueMap {
		const data: KeyValueMap = {};

		if (this.elementAttributes.page) {
			data.url = this.elementAttributes.page;
		}

		queryAll('[name]', this).forEach((field: InputElement) => {
			const name = field.getAttribute('name');
			const value = field.value;

			data[name] = value;
		});

		return data;
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		if (this.hasAttribute('init')) {
			this.submit();
		}

		if (this.hasAttribute('watch')) {
			this.watch();
		}

		if (this.hasAttribute('focus')) {
			setTimeout(() => {
				(query('input') as InputElement).focus();
			}, 0);
		}
	}

	/**
	 * Disable all connected submit buttons.
	 */
	private disbableButtons(): void {
		this.submitButtons.forEach((button) => {
			button.setAttribute('disabled', '');
		});
	}

	/**
	 * Enable all connected submit buttons.
	 */
	private enableButtons(): void {
		this.submitButtons.forEach((button) => {
			button.removeAttribute('disabled');
		});
	}

	/**
	 * Update the class of the containing field to be marked as changed.
	 *
	 * @param element - The input element that has changed
	 * @param changed
	 */
	private updateFieldStatus(element: HTMLElement, changed: boolean): void {
		const field = element.closest(`.${classes.field}`);

		if (field) {
			field.classList.toggle(classes.fieldChanged, changed);
		}
	}

	/**
	 * Reset all fields to be marked as unchanged.
	 */
	private resetFieldStatus(): void {
		queryAll(`.${classes.input}`, this).forEach((input: InputElement) => {
			this.updateFieldStatus(input, false);
		});
	}

	/**
	 * Submit the form.
	 */
	async submit(): Promise<void> {
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
	 * @param response
	 */
	protected processResponse(response: KeyValueMap): void {
		if (response.error) {
			notifyError(response.error);
		}

		if (response.redirect) {
			window.location.href = response.redirect;
		}

		if (response.reload) {
			window.location.reload();
		}
	}

	/**
	 * Watch the form for changes.
	 */
	protected watch(): void {
		const onChange = debounce((event: Event) => {
			this.enableButtons();
			this.updateFieldStatus(event.target as InputElement, true);
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

customElements.define('am-form-submit', FormSubmitComponent);
customElements.define('am-form', FormComponent);
