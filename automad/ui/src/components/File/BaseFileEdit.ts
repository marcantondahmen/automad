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

import { create, listen } from '../../core';
import { File } from '../../types';
import { BaseComponent } from '../Base';
import { ModalComponent } from '../Modal/Modal';

/**
 * A file edit modal toggle component.
 *
 * @extends BaseComponent
 */
export abstract class BaseFileEditComponent extends BaseComponent {
	/**
	 * The file data.
	 */
	data: File;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		listen(this, 'click', () => {
			if (!this.data) {
				console.log('File data is not defined');

				return;
			}

			const modal = this.createModal(this.data);

			setTimeout(() => {
				modal.open();
			}, 0);
		});
	}

	/**
	 * Create a file edit modal component.
	 *
	 * @param file
	 * @returns the created modal component
	 */
	private createModal(file: File): ModalComponent {
		const modal = create('am-modal', [], { destroy: '' }, document.body);

		modal.innerHTML = this.renderModal(file);

		return modal;
	}

	/**
	 * Render the actual modal markup.
	 *
	 * @param file
	 * @returns the modal markup
	 */
	protected renderModal(file: File): string {
		return '';
	}
}
