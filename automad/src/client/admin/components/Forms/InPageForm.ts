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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { InPageBindings, KeyValueMap } from '@/admin/types';
import { FormComponent } from './Form';
import {
	App,
	Attr,
	Binding,
	Bindings,
	createField,
	createLabelFromField,
	FieldTag,
	getPrefixMap,
	InPageController,
	query,
	setDocumentTitle,
} from '@/admin/core';
import { ModalComponent } from '@/admin/components/Modal/Modal';
import { EditorJSComponent } from '@/admin/components/EditorJS';

/**
 * The InPage editing form element.
 *
 * @extends FormComponent
 */
export class InPageFormComponent extends FormComponent {
	/**
	 * Get the api attribute already before attributes are observed.
	 */
	protected get api(): string {
		return InPageController.edit;
	}

	/**
	 * Wait for pending requests.
	 */
	protected get parallel(): boolean {
		return false;
	}

	/**
	 * Only enable submit button when input values have changed.
	 */
	protected get watch(): boolean {
		return true;
	}

	/**
	 * Enable self init.
	 */
	protected get initSelf(): boolean {
		return true;
	}

	/**
	 * The field name.
	 */
	private field: string;

	/**
	 * The origin page to return to.
	 */
	private page: string;

	/**
	 * The page that contains the value.
	 */
	private context: string;

	/**
	 * The page bindings object.
	 */
	private bindings: InPageBindings;

	/**
	 * Initialize the form.
	 */
	protected init(): void {
		const params = new URLSearchParams(document.location.search);

		this.field = params.get('field');
		this.page = params.get('page');
		this.context = params.get('context');

		this.additionalData = {
			field: this.field,
			context: this.context,
			init: true,
		};

		this.setAttribute(Attr.api, this.api);

		setDocumentTitle(
			`${App.pages[this.context]?.title ?? '404'} (${this.field})`
		);

		this.bindings = {
			inPageReturnUrlBinding: new Binding('inPageReturnUrl', {
				initial: this.page,
				modifier: (url: string) => `${App.baseIndex}${url}`,
			}),
			inPageTitleBinding: new Binding('inPageTitle', {
				initial: App.pages[this.context]?.title ?? '404',
			}),
			inPageContextUrlBinding: new Binding('inPageContextUrl', {
				initial: this.context,
				modifier: (url: string) => `${App.baseIndex}${url}`,
			}),
			inPageFieldBinding: new Binding('inPageField', {
				initial: this.field,
				modifier: (field: string) => createLabelFromField(field),
			}),
		};

		Bindings.connectElements(App.root);

		this.listen(window, 'keydown', (event: KeyboardEvent) => {
			if (event.keyCode !== 27) {
				return;
			}

			if (query<ModalComponent>(`[${Attr.modalOpen}]`)) {
				return;
			}

			event.preventDefault();
			event.stopImmediatePropagation();

			window.location.href = this.bindings.inPageReturnUrlBinding.value;
		});

		this.listen(document, 'click', (event: Event) => {
			const target = event.target as HTMLElement;

			if (target.contains(this)) {
				query('[contenteditable], input, textarea', this)?.focus();
			}
		});
	}

	/**
	 * Create the form after the response was received successfully.
	 *
	 * @param response - the response data
	 * @async
	 */
	protected async processResponse(response: KeyValueMap): Promise<void> {
		this.additionalData.init = false;

		if (response.code !== 200) {
			return;
		}

		if (!response.data) {
			return;
		}

		if (response.data.saved) {
			window.location.href = this.bindings.inPageReturnUrlBinding.value;
		}

		if (typeof response.data.value != 'undefined') {
			this.render(response);
		}
	}

	/**
	 * Create the actual form fields.
	 *
	 * @param response
	 */
	private render(response: KeyValueMap): void {
		const prefixMap = getPrefixMap(true);
		let fieldType: FieldTag = FieldTag.textarea;

		for (const [prefix, value] of Object.entries(prefixMap)) {
			if (this.field.startsWith(prefix)) {
				fieldType = value;
				break;
			}
		}

		this.innerHTML = '';

		createField(fieldType, this, {
			key: this.field,
			value: response.data.value,
			name: 'value',
			isInPage: true,
		});

		this.additionalData['dataFetchTime'] = response.time;
	}
}

customElements.define('am-inpage-form', InPageFormComponent);
