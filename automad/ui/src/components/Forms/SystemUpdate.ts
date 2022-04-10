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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, classes, create, html, listen, query } from '../../core';
import { KeyValueMap, SystemUpdateResponse } from '../../types';
import { ModalComponent } from '../Modal/Modal';
import { FormComponent } from './Form';

/**
 * The system update form.
 *
 * @extends FormComponent
 */
export class SystemUpdateComponent extends FormComponent {
	/**
	 * The progress modal.
	 */
	private progressModal: ModalComponent;

	/**
	 * The states object maps states to render methods.
	 */
	private get states(): KeyValueMap {
		return {
			disabled: this.renderDisabled,
			notSupported: this.renderNotSupported,
			success: this.renderSuccess,
			connectionError: this.renderConnectionError,
			pending: this.renderPending,
			upToDate: this.renderUpToDate,
		};
	}

	/**
	 * Allow parallel requests.
	 */
	protected get parallel(): boolean {
		return false;
	}

	/**
	 * The form inits itself when created.
	 */
	protected get initSelf(): boolean {
		return true;
	}

	/**
	 * Process the response that is received after submitting the form.
	 *
	 * @param response
	 * @async
	 */
	protected async processResponse(response: KeyValueMap): Promise<void> {
		super.processResponse(response);

		if (this.progressModal) {
			this.progressModal.close();
		}

		if (!response.data) {
			return;
		}

		const data: SystemUpdateResponse = response.data;

		if (data.state && Object.keys(this.states).includes(data.state)) {
			this.states[data.state].apply(this, [data]);
		}
	}

	/**
	 * Render the alert for development repositories.
	 *
	 * @param data
	 */
	private renderDisabled(data: SystemUpdateResponse): void {
		this.innerHTML = html`
			<am-alert
				icon="slash-circle"
				text="systemUpdateDisabled"
				type="danger"
			></am-alert>
		`;
	}

	/**
	 * Render an alert box for unsupported systems such as PHP with missing modules.
	 *
	 * @param data
	 */
	private renderNotSupported(data: SystemUpdateResponse): void {
		this.innerHTML = html`
			<am-alert
				icon="slash-circle"
				text="systemUpdateNotSupportedError"
				type="danger"
			></am-alert>
		`;
	}

	/**
	 * Render the success alert after successfully updating the system.
	 *
	 * @param data
	 */
	private renderSuccess(data: SystemUpdateResponse): void {
		this.renderUpToDate(data);

		const modal = create(
			'am-modal',
			[],
			{ noesc: '', noclick: '', destroy: '' },
			this
		) as ModalComponent;

		modal.innerHTML = html`
			<div class="${classes.modalDialog}">
				${App.text('systemUpdateSuccess')}
				${App.text('systemUpdateCurrentVersion')}
				<strong>${data.current}</strong>.
				<div class="${classes.modalFooter}">
					<a
						href="${App.dashboardURL}"
						class="${classes.button} ${classes.buttonSuccess}"
					>
						${App.text('systemUpdateSuccessReload')}
					</a>
				</div>
			</div>
		`;

		modal.open();
	}

	/**
	 * Render the alert box for connection errors.
	 *
	 * @param data
	 */
	private renderConnectionError(data: SystemUpdateResponse): void {
		this.innerHTML = html`
			<am-alert
				icon="hdd-network"
				text="systemUpdateConnectionError"
				type="danger"
			></am-alert>
		`;
	}

	/**
	 * Render the form for pending updates.
	 *
	 * @param data
	 */
	private renderPending(data: SystemUpdateResponse): void {
		const renderItems = (items: string[]) => {
			let list = '';

			items.forEach((item) => {
				list += html`<li>${item}</li>`;
			});

			return html`<ul>
				${list}
			</ul>`;
		};

		this.innerHTML = html`
			<input type="hidden" name="update" value="run" />
			<div class="${classes.alert} ${classes.alertSuccess}">
				<div class="${classes.alertIcon}">
					<i class="bi bi-arrow-repeat"></i>
				</div>
				<div class="${classes.alertText}">
					<p>
						${App.text('systemUpdateCurrentVersion')}
						<strong>${data.current}</strong>.
						<br />
						${App.text('systemUpdateAvailable')}
					</p>
					<am-submit
						class="${classes.button} ${classes.buttonSuccess}"
					>
						${App.text('systemUpdateTo')}
						<strong>${data.latest} </strong>
					</am-submit>
				</div>
			</div>
			<p>${App.text('systemUpdateItems')}:</p>
			${renderItems(data.items)}
		`;

		const submit = query('am-submit', this);

		listen(submit, 'click', () => {
			this.progressModal = create(
				'am-modal',
				[],
				{ noesc: '', noclick: '', destroy: '' },
				App.root
			) as ModalComponent;

			this.progressModal.innerHTML = html`
				<div class="${classes.modalDialog}">
					<span class="${classes.spinner}"></span>
					${App.text('systemUpdateProgress')}
				</div>
			`;

			this.progressModal.open();
		});
	}

	/**
	 * Render the alert for systems that are up to date.
	 */
	private renderUpToDate(data: SystemUpdateResponse): void {
		this.innerHTML = html`
			<am-alert
				icon="check-circle"
				text="systemUpToDate"
				type="success"
			></am-alert>
		`;
	}
}

customElements.define('am-system-update', SystemUpdateComponent);
