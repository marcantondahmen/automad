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
import { ComponentImplementation, MailInput } from '../types';

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

/**
 * A simple mail form.
 */
export default class MailComponent implements ComponentImplementation {
	/**
	 * The main element.
	 */
	element: HTMLElement;

	/**
	 * The class constructor.
	 */
	constructor(element: HTMLElement) {
		this.element = element;

		// Wait 2 second in order prevent spam bot submissions.
		setTimeout(this.init.bind(this), 2000);
	}

	/**
	 * Initialize the form.
	 */
	private init(): void {
		const button = query<HTMLButtonElement>('button', this.element);
		const inputs = queryAll<MailInput>('[name]', this.element);

		button.addEventListener('click', async () => {
			this.element.classList.add(cls.validate);

			if (!checkInputs(inputs)) {
				return;
			}

			try {
				const response = await fetch(window.location.href, {
					method: 'POST',
					body: this.getData(inputs),
				});

				const { data } = await response.json();

				if (data.status) {
					const message =
						query('p', this.element) ??
						create('p', [cls.message], {}, this.element, '', true);

					message.textContent = data.status;

					resetInputs(inputs);
					this.element.classList.remove(cls.validate);
				}
			} catch {}
		});
	}

	/**
	 * Get the input field data along with the block id.
	 */
	private getData(inputs: MailInput[]): FormData {
		const formData: FormData = new FormData();

		inputs.forEach((input) => {
			formData.append(input.name, input.value);
		});

		formData.append('id', this.element.id);

		return formData;
	}
}
