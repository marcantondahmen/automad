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

import { App, Attr, create, CSS, html, listen, query } from '../../core';
import { KeyValueMap, SystemUpdateResponse } from '../../types';
import { ModalComponent } from '../Modal/Modal';
import { FormComponent } from './Form';

/**
 * The system update form.
 *
 * @extends FormComponent
 */
export class SystemUpdateFormComponent extends FormComponent {
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
				${Attr.icon}="slash-circle"
				${Attr.text}="systemUpdateDisabled"
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
				${Attr.icon}="slash-circle"
				${Attr.text}="systemUpdateNotSupportedError"
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
			{ [Attr.noEsc]: '', [Attr.noClick]: '', [Attr.destroy]: '' },
			this
		) as ModalComponent;

		modal.innerHTML = html`
			<div class="${CSS.modalDialog}">
				<div class="${CSS.modalBody}">
					${App.text('systemUpdateSuccess')}
				</div>
				<div class="${CSS.modalFooter}">
					<a
						href="${App.dashboardURL}"
						class="${CSS.button} ${CSS.buttonAccent}"
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
				${Attr.icon}="hdd-network"
				${Attr.text}="systemUpdateConnectionError"
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

			return html`<ul class="${CSS.textParagraph}">
				${list}
			</ul>`;
		};

		this.innerHTML = html`
			<div class="${CSS.alert}">
				<input type="hidden" name="update" value="run" />
				<div class="${CSS.alertIcon}">
					<i class="bi bi-download"></i>
				</div>
				<div class="${CSS.alertText}">
					<div>
						${App.text('systemUpdateCurrentVersion')}
						<strong>${data.current}</strong>.
						<br />
						${App.text('systemUpdateAvailable')}
					</div>
					<div>
						<am-submit class="${CSS.button} ${CSS.buttonAccent}">
							${App.text('systemUpdateTo')}
							<span class="${CSS.badge}">${data.latest}</span>
						</am-submit>
						<a
							href="https://automad.org/release-notes"
							class="${CSS.button} ${CSS.buttonLink}"
							target="_blank"
						>
							Release Notes
						</a>
					</div>
				</div>
			</div>
			<div>
				<p>${App.text('systemUpdateItems')}</p>
				${renderItems(data.items)}
			</div>
		`;

		const submit = query('am-submit', this);

		listen(submit, 'click', () => {
			this.progressModal = create(
				'am-modal',
				[],
				{ [Attr.noEsc]: '', [Attr.noClick]: '', [Attr.destroy]: '' },
				App.root
			) as ModalComponent;

			this.progressModal.innerHTML = html`
				<div class="${CSS.modalDialog}">
					<div class="${CSS.modalSpinner}">
						<span class="${CSS.modalSpinnerIcon}"></span>
						<span class="${CSS.modalSpinnerText}">
							${App.text('systemUpdateProgress')}
						</span>
					</div>
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
				${Attr.icon}="check-circle"
				${Attr.text}="systemUpToDate"
			></am-alert>
		`;
	}
}

customElements.define('am-system-update-form', SystemUpdateFormComponent);
