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

import { App, classes, eventNames, html, listen, query } from '../../core';
import { KeyValueMap } from '../../types';
import { FormComponent } from './Form';

/**
 * The config file edit form.
 *
 * @extends FormComponent
 */
export class ConfigFileFormComponent extends FormComponent {
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
	 * Initialize the form.
	 */
	protected init(): void {
		super.init();

		this.listeners.push(
			listen(window, eventNames.appStateChange, () => {
				this.innerHTML = '';
				this.submit();
			})
		);
	}

	/**
	 * Process the response that is received after submitting the form.
	 *
	 * @param response
	 * @async
	 */
	protected async processResponse(response: KeyValueMap): Promise<void> {
		super.processResponse(response);

		if (response.data.content) {
			this.render(response.data.content);
		}
	}

	/**
	 * Render the actual textarea.
	 *
	 * @param content
	 */
	private render(content: string): void {
		this.innerHTML = html`
			<am-modal-toggle
				class="${classes.button}"
				modal="#am-config-file-form-modal"
			>
				<am-icon-text
					icon="pencil"
					text="${App.text('systemConfigFileEdit')}"
				></am-icon-text>
			</am-modal-toggle>
			<am-modal id="am-config-file-form-modal">
				<div class="${classes.modalDialog}">
					<div
						class="${classes.modalHeader} ${classes.flex} ${classes.flexAlignCenter}"
					>
						<div class="${classes.flexItemGrow}">
							${App.text('systemConfigFileEdit')}
						</div>
						<am-modal-close class="${classes.button}">
							${App.text('close')}
						</am-modal-close>
						<am-submit class="${classes.button}">
							${App.text('save')}
						</am-submit>
					</div>
					<textarea
						class="${classes.input}"
						name="content"
					></textarea>
				</div>
			</am-modal>
		`;

		(query('textarea', this) as HTMLInputElement).value = content;
	}
}

customElements.define('am-config-file-form', ConfigFileFormComponent);
