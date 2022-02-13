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
	App,
	classes,
	debounce,
	getPageURL,
	listen,
	notifyError,
	notifySuccess,
	query,
	queryAll,
	requestAPI,
} from '../../core';
import { InputElement, KeyValueMap } from '../../types';
import { BaseComponent } from '../Base';

/**
 * A basic form.
 *
 * The following options are available and can be passed as attributes:
 * - `api` (required)
 * - `init`
 * - `watch`
 * - `focus`
 * - `enter`
 * - `confirm`
 *
 * Self initialized form with watched submit button:
 *
 * @example
 * <am-form api="Class/method" init watch></am-form>
 *
 * Focus the first input of a for when being connected:
 *
 * @example
 * <am-form api="Class/method" focus>
 *     <input>
 * </am-form>
 *
 * @extends BaseComponent
 */
export class FormComponent extends BaseComponent {
	/**
	 * The change state of the form.
	 */
	hasUnsavedChanges: boolean = false;

	/**
	 * Changes are watched.
	 */
	protected watchChanges: boolean = false;

	/**
	 * Get the api attribute already before attributes are observed.
	 */
	protected get api(): string {
		return this.getAttribute('api');
	}

	/**
	 * The confirm modal message.
	 */
	protected get confirm(): string {
		return this.getAttribute('confirm');
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
		const external = queryAll(`am-submit[form="${this.api}"]`);
		const internal = queryAll('am-submit:not([form])', this);

		return external.concat(internal);
	}

	/**
	 * The array of valied field selectors that are used to collect the form data.
	 */
	protected get controls(): string[] {
		return [
			'input[type="text"]',
			'input[type="password"]',
			'input[type="datetime-local"]',
			'input[type="hidden"]',
			'input[type="checkbox"]:checked',
			'input[type="radio"]:checked',
			'textarea',
			'select',
			'am-editor',
		];
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

		queryAll(this.controls.join(','), this).forEach(
			(field: InputElement) => {
				const name = field.getAttribute('name');
				const value = field.value;

				if (name) {
					data[name] = value;
				}
			}
		);

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
			this.submit(true);
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
	 * @param skipConfirmOnInit
	 * @async
	 */
	async submit(skipConfirmOnInit: boolean = false): Promise<void> {
		if (
			!skipConfirmOnInit &&
			this.confirm &&
			!window.confirm(this.confirm)
		) {
			return null;
		}

		const response = await requestAPI(this.api, this.formData);

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
		if (response.redirect) {
			App.root.setView(response.redirect);
		}

		if (response.reload) {
			window.location.reload();
		}

		if (response.error) {
			notifyError(response.error);
		}

		if (response.success) {
			notifySuccess(response.success);
		}

		if (response.debug) {
			const log: KeyValueMap = {};

			log[`API: ${this.api}`] = response.debug;
			console.log(log);
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
			'input, textarea, am-editor'
		);

		listen(this, 'change', onChangeHandler.bind(this), 'select, am-editor');

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
