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
 * Copyright (c) 2021-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	CSS,
	EventName,
	fire,
	getFormData,
	query,
	setFormData,
} from '../../core';
import { KeyValueMap } from '../../types';
import { BaseWindowComponent } from './BaseWindow';

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
 * @extends BaseWindowComponent
 */
export class ModalComponent extends BaseWindowComponent {
	/**
	 * The tag name for the component.
	 *
	 * @static
	 * @readonly
	 */
	static readonly TAG_NAME = 'am-modal';

	/**
	 * The form data of the form controls included in the modal.
	 */
	private formData: KeyValueMap;

	/**
	 * The internal navigation lock id.
	 */
	private lockId: number;

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
		super.close();

		this.toggleBodyOverflow();
		this.restoreInitialFormData();

		fire(EventName.modalClose, this);

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
		super.open();

		this.lockNavigation();
		this.toggleBodyOverflow();
		this.saveInitialFormData();

		fire(EventName.modalOpen, this);

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

customElements.define(ModalComponent.TAG_NAME, ModalComponent);
