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
	confirm,
	debounce,
	fire,
	FormDataProviders,
	getFormData,
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
import { ModalComponent } from '../Modal/Modal';

export const autoSubmitTimeout = 750;

const debounced = debounce(async (callback: Function): Promise<void> => {
	await callback();
}, autoSubmitTimeout);

/**
 * A basic form.
 *
 * The following options are available and can be passed as attributes:
 * - `api` (required) - the API endpoint
 * - `focus` - focus first input when connected
 * - `enter` - submit using enter key
 * - `confirm` - require confirmation before submitting
 * - `event` - fire event when receiving the API response
 * - `auto` - automatically submit form on change
 *
 * Focus the first input of a for when being connected:
 *
 * @example
 * <am-form api="Class/method" focus>
 *     <input>
 * </am-form>
 *
 * Fire an event on the window after getting a response from the server:
 *
 * @example
 * <am-form api="File/import" event="FileCollectionUpdate">
 *     <input>
 * </am-form>
 *
 * @extends BaseComponent
 */
export class FormComponent extends BaseComponent {
	/**
	 * Additional data that can be added to the form data object.
	 */
	additionalData: KeyValueMap = {};

	/**
	 * Allow parallel requests.
	 */
	protected get parallel(): boolean {
		return true;
	}

	/**
	 * Get the api attribute already before attributes are observed.
	 */
	protected get api(): string {
		return this.getAttribute('api');
	}

	/**
	 * Submit form data on changes.
	 */
	protected get auto(): boolean {
		return this.hasAttribute('auto');
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
		return false;
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
	 * The form data object.
	 */
	get formData(): KeyValueMap {
		const data: KeyValueMap = Object.assign(
			{},
			this.additionalData,
			getFormData(this)
		);

		const pageUrl = getPageURL();

		if (pageUrl) {
			data.url = pageUrl;
		}

		return data;
	}

	/**
	 * Get the parent modal if existing.
	 */
	get parentModal(): ModalComponent {
		const modal = this.closest('am-modal') as ModalComponent;

		return modal || null;
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
	protected init(): void {
		if (this.initSelf) {
			this.submit(true);
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

		this.watch();
	}

	/**
	 * Submit the form.
	 *
	 * @param skipConfirmOnInit
	 * @async
	 */
	async submit(skipConfirmOnInit: boolean = false): Promise<void> {
		if (!skipConfirmOnInit && this.confirm) {
			const isConfirmed = await confirm(this.confirm);

			if (!isConfirmed) {
				return;
			}
		}

		const lockId = App.addNavigationLock();

		if (this.verifyRequired()) {
			await requestAPI(
				this.api,
				this,
				this.parallel,
				async (data: KeyValueMap) => {
					await this.processResponse(data);

					if (this.hasAttribute('event')) {
						fire(this.getAttribute('event'));
					}

					const modal = this.parentModal;

					if (modal) {
						modal.close();
					}

					App.removeNavigationLock(lockId);
				}
			);
		} else {
			App.removeNavigationLock(lockId);
		}
	}

	/**
	 * Process the response that is received after submitting the form.
	 *
	 * @param response
	 * @async
	 */
	protected async processResponse(response: KeyValueMap): Promise<void> {
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
	 */
	onChange(): void {
		if (this.auto) {
			debounced(async () => {
				const lockId = App.addNavigationLock();

				await this.submit();

				App.removeNavigationLock(lockId);
			});

			const lockId = App.addNavigationLock();

			setTimeout(() => {
				App.removeNavigationLock(lockId);
			}, autoSubmitTimeout + 10);
		}
	}

	/**
	 * Watch the form for changes.
	 */
	protected watch(): void {
		listen(
			this,
			'change keydown cut paste drop input',
			this.onChange.bind(this),
			FormDataProviders.selector
		);
	}

	/**
	 * Verifies that all required fields have values.
	 *
	 * @returns true if all required fields have values
	 */
	private verifyRequired(): boolean {
		const requiredInputs: HTMLElement[] = queryAll('[required]', this);
		const requiredEmpty: InputElement[] = [];

		requiredInputs.forEach((input: InputElement) => {
			if (!input.value.trim()) {
				requiredEmpty.push(input);
			}
		});

		if (requiredEmpty.length) {
			requiredEmpty[0].focus();
			return false;
		}

		return true;
	}
}

customElements.define('am-form', FormComponent);
