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

import {
	classes,
	debounce,
	getPageURL,
	listen,
	query,
	queryAll,
} from '../../core/utils';
import { notifyError } from '../../core/notify';
import { requestController } from '../../core/request';
import { InputElement, KeyValueMap } from '../../types';
import { BaseComponent } from '../Base';

/**
 * A basic form.
 *
 * The following options are available and can be passed as attributes:
 * - `controller` (required)
 * - `init`
 * - `watch`
 * - `focus`
 * - `enter`
 *
 * Self initialized form with watched submit button:
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
	 * The change state of the form.
	 */
	protected hasUnsavedChanges: boolean = false;

	/**
	 * Changes are watched.
	 */
	protected watchChanges: boolean = false;

	/**
	 * Get the controller attribute already before attributes are observed.
	 */
	protected get controller(): string {
		return this.getAttribute('controller');
	}

	/**
	 * The form inits itself when created.
	 */
	protected get initSelf(): boolean {
		return this.hasAttribute('init');
	}

	/**
	 * All related submit buttons.
	 */
	protected get submitButtons(): HTMLElement[] {
		return queryAll(
			`am-submit[form="${this.elementAttributes.controller}"]`
		);
	}

	/**
	 * The form data object.
	 */
	get formData(): KeyValueMap {
		const data: KeyValueMap = {};
		const pageUrl = getPageURL();

		if (pageUrl) {
			data.url = pageUrl;
		}

		queryAll('[name]', this).forEach((field: InputElement) => {
			const name = field.getAttribute('name');
			const value = field.value;

			data[name] = value;
		});

		return data;
	}

	/**
	 * The form constructor.
	 */
	constructor() {
		super();

		this.init();
	}

	/**
	 * Initialize the form.
	 */
	protected async init(): Promise<void> {
		if (this.initSelf) {
			this.submit();
		}

		if (this.hasAttribute('watch')) {
			this.watchChanges = true;
			this.watch();
		}

		if (this.hasAttribute('focus')) {
			setTimeout(() => {
				(query('input') as InputElement).focus();
			}, 0);
		}

		if (this.hasAttribute('enter')) {
			listen(
				this,
				'keydown',
				(event: KeyboardEvent) => {
					if (event.which == 13) {
						event.preventDefault();
						this.submit();
					}
				},
				`.${classes.input}`
			);
		}
	}

	/**
	 * Disable all connected submit buttons.
	 */
	private disbableButtons(): void {
		setTimeout(() => {
			this.submitButtons.forEach((button) => {
				button.setAttribute('disabled', '');
			});
		}, 200);
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
		setTimeout(() => {
			queryAll(`.${classes.input}`, this).forEach(
				(input: InputElement) => {
					this.updateFieldStatus(input, false);
				}
			);
		}, 200);
	}

	/**
	 * Submit the form.
	 *
	 * @async
	 */
	async submit(): Promise<void> {
		const response = await requestController(
			this.controller,
			this.formData
		);

		if (this.watchChanges) {
			this.disbableButtons();
		}

		this.hasUnsavedChanges = false;
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
	 * The callback that is called when a form input has changed.
	 *
	 * @param input
	 */
	onChange(input: InputElement): void {
		this.enableButtons();
		this.updateFieldStatus(input, true);
		this.hasUnsavedChanges = true;
	}

	/**
	 * Watch the form for changes.
	 */
	protected watch(): void {
		const onChangeHandler = debounce((event: Event) => {
			this.onChange(event.target as InputElement);
		}, 100);

		listen(
			this,
			'keydown cut paste drop input',
			onChangeHandler.bind(this),
			'input, textarea'
		);

		listen(this, 'change', onChangeHandler.bind(this), 'select');

		listen(window, 'beforeunload', (event: Event) => {
			if (this.hasUnsavedChanges) {
				event.preventDefault();
				event.returnValue = true;
			} else {
				delete event['returnValue'];
			}
		});

		setTimeout(this.disbableButtons.bind(this), 0);
	}
}

customElements.define('am-form', FormComponent);
