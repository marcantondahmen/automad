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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	classes,
	fire,
	getFormData,
	listen,
	query,
	setFormData,
} from '../../core';
import { KeyValueMap } from '../../types';
import { BaseComponent } from '../Base';

export const modalOpenEventName = 'AutomadModalOpen';
export const modalCloseEventName = 'AutomadModalClose';

/**
 * A modal component.
 * The following attributes can be added to a modal component:
 * - `noesc` - Disable the ESC key
 * - `noclick` - Disable closing the modal by clicking on the overlay
 * - `destroy` - Self destroy on close
 *
 * @example
 * <am-modal-toggle modal="#modal">
 *     Open
 * </am-modal-toggle>
 * <am-modal id="modal" noesc noclick>
 *     <div class="am-c-modal__dialog">
 *         <div class="am-c-modal__header">
 *             <span>Title</span>
 *             <am-modal-close>Close</am-modal-close>
 *         </div>
 *     </div>
 * </am-modal>
 *
 * @extends BaseComponent
 */
export class ModalComponent extends BaseComponent {
	/**
	 * The form data of the form controls included in the modal.
	 */
	private formData: KeyValueMap;

	/**
	 * The internal navigation lock id.
	 */
	private lockId: number;

	/**
	 * True if the modal dialog is open.
	 */
	get isOpen(): boolean {
		return this.matches(`.${classes.modalOpen}`);
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(classes.modal);

		if (!this.hasAttribute('noclick')) {
			listen(this, 'click', (event: MouseEvent) => {
				if (this === event.target) {
					this.close();
				}
			});
		}

		if (!this.hasAttribute('noesc')) {
			listen(window, 'keydown', (event: KeyboardEvent) => {
				if (this.isOpen && event.keyCode == 27) {
					this.close();
				}
			});
		}
	}

	/**
	 * Toggle the modal.
	 */
	toggle(): void {
		if (this.isOpen) {
			this.close();
		} else {
			this.open();
		}
	}

	/**
	 * Close the modal.
	 */
	close(): void {
		this.classList.remove(classes.modalOpen);
		this.toggleBodyOverflow();
		this.restoreInitialFormData();

		fire(modalCloseEventName, this);

		if (this.hasAttribute('destroy')) {
			setTimeout(() => {
				this.remove();
			}, 400);
		}

		App.removeNavigationLock(this.lockId);
	}

	/**
	 * Open the modal.
	 */
	open(): void {
		this.lockId = App.addNavigationLock();

		this.classList.add(classes.modalOpen);
		this.toggleBodyOverflow();
		this.saveInitialFormData();

		fire(modalOpenEventName, this);

		const input = query('input, textarea', this);

		if (input) {
			input.focus();
		}
	}

	/**
	 * Toggle the body overflow class when the modal is open.
	 */
	private toggleBodyOverflow(): void {
		const body = query('body');

		body.classList.toggle(
			classes.overflowHidden,
			query(`.${classes.modalOpen}`) != null
		);
	}

	/**
	 * Save the initial form values on opening.
	 */
	private saveInitialFormData(): void {
		this.formData = getFormData(this);
	}

	/**
	 * Restore the initial form data when modal was opened.
	 */
	private restoreInitialFormData(): void {
		setFormData(this.formData, this);
	}
}

customElements.define('am-modal', ModalComponent);
