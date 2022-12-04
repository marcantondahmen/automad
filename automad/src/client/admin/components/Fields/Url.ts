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
 * Copyright (c) 2021-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { CSS, create, html, listen, eventNames, App } from '../../core';
import { AutocompleteComponent } from '../Autocomplete';
import { ModalComponent } from '../Modal/Modal';
import { BaseFieldComponent } from './BaseField';

/**
 * An URL field.
 *
 * @extends BaseFieldComponent
 */
class URLComponent extends BaseFieldComponent {
	/**
	 * Create an input field.
	 */
	protected createInput(): void {
		const { name, id, value, placeholder } = this._data;
		const modal = this.createModal();
		const combo = create('div', [CSS.inputCombo], {}, this);

		create(
			'input',
			[CSS.input],
			{ id, name, value, type: 'text', placeholder },
			combo
		);

		const button = create('span', [CSS.inputComboButton], {}, combo);

		button.innerHTML = html`<i class="bi bi-link"></i>`;

		listen(button, 'click', () => {
			modal.open();
		});
	}

	/**
	 * Create the autocomplete modal element.
	 *
	 * @returns the modal component
	 */
	private createModal(): ModalComponent {
		const modal = create('am-modal', [], {}, this);
		const dialog = create('div', [CSS.modalDialog], {}, modal);
		const header = create('div', [CSS.modalHeader], {}, dialog);
		const body = create('div', [CSS.modalBody], {}, dialog);
		const footer = create('div', [CSS.modalFooter], {}, dialog);

		const select = () => {
			this.input.value = autocomplete.input.value;
			modal.close();
		};

		const autocomplete = create(
			'am-autocomplete',
			[],
			{},
			body
		) as AutocompleteComponent;

		create(
			'am-modal-close',
			[CSS.button, CSS.buttonLink],
			{},
			footer
		).textContent = App.text('cancel');

		const buttonOk = create(
			'button',
			[CSS.button, CSS.buttonAccent],
			{},
			footer
		);

		header.innerHTML = html`
			<span>${this._data.label}</span>
			<am-modal-close class="${CSS.modalClose}"></am-modal-close>
		`;

		buttonOk.textContent = App.text('ok');

		listen(modal, eventNames.modalOpen, () => {
			autocomplete.input.value = '';
		});

		listen(buttonOk, 'click', select.bind(this));
		listen(autocomplete, eventNames.autocompleteSelect, select.bind(this));

		return modal;
	}
}

customElements.define('am-url', URLComponent);
