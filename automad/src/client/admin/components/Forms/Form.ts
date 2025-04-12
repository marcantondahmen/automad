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

import {
	App,
	Attr,
	confirm,
	CSS,
	debounce,
	fire,
	FormDataProviders,
	getLogger,
	collectFieldData,
	getPageURL,
	listen,
	notifyError,
	notifySuccess,
	query,
	queryAll,
	requestAPI,
} from '@/admin/core';
import {
	DeduplicationSettings,
	InputElement,
	KeyValueMap,
} from '@/admin/types';
import { BaseComponent } from '@/admin/components/Base';
import { ModalComponent } from '@/admin/components/Modal/Modal';
import { SubmitComponent } from './Submit';
import { FormErrorComponent } from './FormError';

export const autoSubmitTimeout = 750;

const debounced = debounce(
	async (callback: (...args: any[]) => void): Promise<void> => {
		callback();
	},
	autoSubmitTimeout
);

/**
 * A basic form.
 *
 * The following options are available and can be passed as attributes:
 * - Attr.api (required) - the API endpoint controller
 * - Attr.focus - focus first input when connected
 * - Attr.enter - submit using enter key
 * - Attr.confirm - require confirmation before submitting
 * - Attr.event - fire event when receiving the API response
 * - Attr.auto - automatically submit form on change
 * - Attr.watch - disable submit buttons until changes are made
 *
 * Focus the first input of a for when being connected:
 *
 * @example
 * <am-form ${Attr.api}="Controller::method" ${Attr.focus}>
 *     <input>
 * </am-form>
 *
 * Fire an event on the window after getting a response from the server:
 *
 * @example
 * <am-form ${Attr.api}="FileController::import" ${Attr.event}="FileCollectionUpdate">
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
	 * Cache the last submitted form data in order to identify duplicate submissions.
	 */
	private lastSubmittedFormData: string = '';

	/**
	 * The deduplication settings for the form.
	 */
	protected get deduplicationSettings(): DeduplicationSettings {
		return {
			getFormData: null,
			enabled: false,
		};
	}

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
		return this.getAttribute(Attr.api);
	}

	/**
	 * Submit form data on changes.
	 */
	protected get auto(): boolean {
		return this.hasAttribute(Attr.auto);
	}

	/**
	 * The confirm modal message.
	 */
	protected get confirm(): string {
		return this.getAttribute(Attr.confirm);
	}

	/**
	 * Only enable submit button when input values have changed.
	 */
	protected get watch(): boolean {
		return this.hasAttribute(Attr.watch);
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
		const external = queryAll(`am-submit[${Attr.form}="${this.api}"]`);
		const internal = queryAll(`am-submit:not([${Attr.form}])`, this);

		return external.concat(internal);
	}

	/**
	 * The form data object.
	 */
	get formData(): KeyValueMap {
		const data: KeyValueMap = Object.assign(
			{},
			this.additionalData,
			collectFieldData(this)
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
		const modal = this.closest<ModalComponent>('am-modal');

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

		if (this.hasAttribute(Attr.focus)) {
			setTimeout(() => {
				query<InputElement>('input').focus();
			}, 0);
		}

		if (this.hasAttribute(Attr.enter)) {
			listen(
				this,
				'keydown',
				(event: KeyboardEvent) => {
					if (event.which == 13) {
						event.preventDefault();
						this.submit();
					}
				},
				`.${CSS.input}`
			);
		}

		this.disableSubmitButtons();
		this.watchChanges();
	}

	/**
	 * Disable submit button for watched forms.
	 */
	disableSubmitButtons(): void {
		if (this.watch) {
			setTimeout(() => {
				this.submitButtons.forEach((button: SubmitComponent) => {
					button.setAttribute('disabled', '');
					button.blur();
				});
			}, 0);
		}
	}

	/**
	 * Optionally test for duplicate form submissions.
	 *
	 * @return boolean
	 */
	isDuplicateSubmission(): boolean {
		if (!this.deduplicationSettings.enabled) {
			return false;
		}

		const data = this.deduplicationSettings.getFormData(this);

		if (
			Object.keys(data).length &&
			JSON.stringify(data) === this.lastSubmittedFormData
		) {
			getLogger().log('Form data has not changed');

			return true;
		}

		this.lastSubmittedFormData = JSON.stringify(data);

		return false;
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

		if (this.isDuplicateSubmission()) {
			return;
		}

		const lockId = App.addNavigationLock();

		this.submitButtons.forEach((button) => {
			if (button.classList.contains(CSS.button)) {
				button.classList.add(CSS.buttonLoading);
			}
		});

		queryAll(
			'input:not([type="hidden"], textarea, [contenteditable])',
			this
		).forEach((input) => {
			input.classList.add(CSS.validate);
		});

		if (this.verifyRequired()) {
			await requestAPI(
				this.api,
				this,
				this.parallel,
				async (data: KeyValueMap) => {
					await this.processResponse(data);

					if (this.hasAttribute(Attr.event)) {
						fire(this.getAttribute(Attr.event));
					}

					App.removeNavigationLock(lockId);
				}
			);
		} else {
			App.removeNavigationLock(lockId);
		}

		this.submitButtons.forEach((button) => {
			button.classList.remove(CSS.buttonLoading);
		});
	}

	/**
	 * Process the response that is received after submitting the form.
	 *
	 * @param response
	 * @async
	 */
	protected async processResponse(response: KeyValueMap): Promise<void> {
		const { redirect, reload, error, success, debug } = response;

		// Note that empty strings can be used to just soft-reload the dashboard.
		if (typeof redirect != 'undefined') {
			App.root.setView(redirect);
		}

		if (reload) {
			App.reload();
		}

		setTimeout(() => {
			const errorContainer = query<FormErrorComponent>(
				FormErrorComponent.TAG_NAME,
				this
			);

			if (errorContainer) {
				errorContainer.message = error ?? '';
			}

			if (error) {
				if (!errorContainer) {
					notifyError(error);
				}

				getLogger().error(error);
			}
		}, 0);

		if (success) {
			notifySuccess(success);
		}

		if (debug && debug instanceof Array) {
			debug.forEach((item) => {
				getLogger().log(this.api, item);
			});
		}

		if (!error) {
			this.disableSubmitButtons();
			this.parentModal?.close();
		}
	}

	/**
	 * The callback that is called when a form input has changed.
	 */
	onChange(): void {
		if (!App.isReady) {
			// Return early here in case the app is not fully initialized yet and
			// potential changes are triggered by initial values of connected bindings.
			return;
		}

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

		if (this.watch) {
			this.submitButtons.forEach((button: SubmitComponent) => {
				button.removeAttribute('disabled');
			});
		}
	}

	/**
	 * Watch the form for changes.
	 */
	private watchChanges(): void {
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
		const requiredInputs = queryAll<InputElement>('[required]', this);
		const requiredEmpty: InputElement[] = [];

		requiredInputs.forEach((input) => {
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
