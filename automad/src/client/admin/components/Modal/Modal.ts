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

import {
	App,
	Attr,
	CSS,
	eventNames,
	fire,
	getFormData,
	listen,
	query,
	setFormData,
} from '../../core';
import { KeyValueMap } from '../../types';
import { BaseComponent } from '../Base';

/**
 * A modal component.
 * The following attributes can be added to a modal component:
 * - Attr.noEsc - Disable the ESC key
 * - Attr.noClick - Disable closing the modal by clicking on the overlay
 * - Attr.destroy - Self destroy on close
 * - Attr.noFocus - don't focus first input
 *
 * @example
 * <am-modal-toggle ${Attr.modal}="#modal">
 *     Open
 * </am-modal-toggle>
 * <am-modal id="modal" ${Attr.noEsc} ${Attr.noClick}>
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
		return this.matches(`.${CSS.modalOpen}`);
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.modal);

		if (!this.hasAttribute(Attr.noClick)) {
			listen(this, 'click', (event: MouseEvent) => {
				if (this === event.target) {
					this.close();
				}
			});
		}

		if (!this.hasAttribute(Attr.noEsc)) {
			this.listeners.push(
				listen(window, 'keydown', (event: KeyboardEvent) => {
					if (this.isOpen && event.keyCode == 27) {
						this.close();
					}
				})
			);
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
	 * Lock the navigation.
	 */
	protected lockNavigation(): void {
		this.lockId = App.addNavigationLock();
	}

	/**
	 * Unlock the navigation.
	 */
	protected unlockNavigation(): void {
		App.removeNavigationLock(this.lockId);
	}

	/**
	 * Close the modal.
	 */
	close(): void {
		this.classList.remove(CSS.modalOpen);
		this.toggleBodyOverflow();
		this.restoreInitialFormData();

		fire(eventNames.modalClose, this);

		if (this.hasAttribute(Attr.destroy)) {
			setTimeout(() => {
				this.remove();
			}, 400);
		}

		this.unlockNavigation();
	}

	/**
	 * Open the modal.
	 */
	open(): void {
		this.lockNavigation();

		this.classList.add(CSS.modalOpen);
		this.toggleBodyOverflow();
		this.saveInitialFormData();

		fire(eventNames.modalOpen, this);

		if (!this.hasAttribute(Attr.noFocus)) {
			const input = query('input, textarea', this);

			if (input) {
				input.focus();
			}
		}
	}

	/**
	 * Toggle the body overflow class when the modal is open.
	 */
	protected toggleBodyOverflow(): void {
		const body = query('body');

		body.classList.toggle(
			CSS.overflowHidden,
			query(`.${CSS.modalOpen}`) != null
		);
	}

	/**
	 * Save the initial form values on opening.
	 */
	protected saveInitialFormData(): void {
		this.formData = getFormData(this);
	}

	/**
	 * Restore the initial form data when modal was opened.
	 */
	protected restoreInitialFormData(): void {
		setFormData(this.formData, this);
	}
}

customElements.define('am-modal', ModalComponent);
