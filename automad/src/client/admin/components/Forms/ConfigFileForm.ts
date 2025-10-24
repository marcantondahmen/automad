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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	CSS,
	EventName,
	html,
	initTabHandler,
	query,
	Undo,
} from '@/admin/core';
import { KeyValueMap, UndoValue } from '@/admin/types';
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

		this.listen(window, EventName.appStateChange, () => {
			this.innerHTML = '';
			this.submit();
		});
	}

	/**
	 * Process the response that is received after submitting the form.
	 *
	 * @param response
	 * @async
	 */
	protected async processResponse(response: KeyValueMap): Promise<void> {
		super.processResponse(response);

		if (response.data?.content) {
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
				class="${CSS.button} ${CSS.buttonPrimary}"
				${Attr.modal}="#am-config-file-form-modal"
			>
				${App.text('systemConfigFileEdit')}
			</am-modal-toggle>
			<am-modal id="am-config-file-form-modal">
				<am-modal-dialog class="${CSS.modalDialogLarge}">
					<div
						class="${CSS.modalHeader} ${CSS.flex} ${CSS.flexAlignCenter} ${CSS.flexGap}"
					>
						<span>${App.text('systemConfigFileEdit')}</span>
						<span
							class="${CSS.flex} ${CSS.flexAlignCenter} ${CSS.flexGap}"
						>
							<am-modal-close class="${CSS.button}">
								${App.text('cancel')}
							</am-modal-close>
							<am-submit
								class="${CSS.button} ${CSS.buttonPrimary}"
							>
								${App.text('save')}
							</am-submit>
						</span>
					</div>
					<textarea
						class="${CSS.modalCode}"
						name="content"
					></textarea>
				</am-modal-dialog>
			</am-modal>
		`;

		const textarea = query<HTMLTextAreaElement>('textarea', this);

		textarea.value = content;
		this.listeners.push(initTabHandler(textarea));

		Undo.attach({
			getValueProvider: () => textarea,
			query: () => textarea.value,
			mutate: (value: UndoValue) => {
				textarea.value = value;
			},
		});
	}
}

customElements.define('am-config-file-form', ConfigFileFormComponent);
