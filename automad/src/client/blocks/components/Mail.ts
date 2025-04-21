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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { create, query, queryAll } from '@/common';
import { MailInput } from '../types';

const cls = {
	message: 'am-message',
	validate: 'am-validate',
};

const checkInputs = (inputs: MailInput[]): boolean => {
	let isValid = true;

	inputs.forEach((input) => {
		if (!input.checkValidity()) {
			isValid = false;
		}
	});

	return isValid;
};

const resetInputs = (inputs: MailInput[]): void => {
	inputs.forEach((input) => {
		input.value = '';
	});
};

const getInputData = (inputs: MailInput[]): FormData => {
	const formData: FormData = new FormData();

	inputs.forEach((input) => {
		formData.append(input.name, input.value);
	});

	return formData;
};

/**
 * A simple mail form.
 */
export class MailComponent extends HTMLElement {
	/**
	 * The class constructor.
	 */
	constructor() {
		super();
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		// Wait 2 second in order prevent spam bot submissions.
		setTimeout(this.init.bind(this), 2000);
	}

	/**
	 * Initialize the form.
	 */
	private init(): void {
		const button = query<HTMLButtonElement>('button', this);
		const inputs = queryAll<MailInput>('[name]', this);

		button.addEventListener('click', async () => {
			this.classList.add(cls.validate);

			if (!checkInputs(inputs)) {
				return;
			}

			try {
				const response = await fetch(window.location.href, {
					method: 'POST',
					body: getInputData(inputs),
				});

				const { data } = await response.json();

				if (data.status) {
					const message =
						query('p', this) ??
						create('p', [cls.message], {}, this, '', true);

					message.textContent = data.status;

					resetInputs(inputs);
					this.classList.remove(cls.validate);
				}
			} catch {}
		});
	}
}

customElements.define('am-mail', MailComponent);
