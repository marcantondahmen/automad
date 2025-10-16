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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	createFocusTrap,
	CSS,
	EventName,
	fire,
	collectFieldData,
	query,
	setFormData,
	queryAll,
} from '@/admin/core';
import { InputElement, KeyValueMap, Listener } from '@/admin/types';
import { BaseComponent } from '@/admin/components/Base';

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
 *     <am-modal-dialog>
 *         <am-modal-header>...</am-modal-header>
 *         <am-modal-body>...</am-modal-body>
 *         <am-modal-footer>...</am-modal-footer>
 *     </am-modal-dialog>
 * </am-modal>
 *
 * @extends BaseComponent
 */
export class ModalComponent extends BaseComponent {
	/**
	 * The tag name for the component.
	 *
	 * @static
	 */
	static TAG_NAME = 'am-modal';

	/**
	 * The class names for the component.
	 *
	 * @readonly
	 */
	protected readonly classes = [CSS.modal];

	/**
	 * The form data of the form controls included in the modal.
	 */
	private formData: KeyValueMap;

	/**
	 * The internal focus trap listener.
	 */
	private focusTrap: Listener;

	/**
	 * True if the modal dialog is open.
	 */
	get isOpen(): boolean {
		return this.matches(`[${Attr.modalOpen}]`);
	}

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
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(...this.classes);

		if (!this.hasAttribute(Attr.noClick)) {
			this.listen(this, 'click', (event: MouseEvent) => {
				if (this === event.target) {
					this.close();
				}
			});
		}

		if (!this.hasAttribute(Attr.noEsc)) {
			this.listen(window, 'keydown', (event: KeyboardEvent) => {
				if (this.isOpen && event.keyCode == 27) {
					this.close();
				}
			});
		}
	}

	/**
	 * Toggle the body overflow class when the modal is open.
	 */
	protected toggleBodyOverflow(): void {
		const body = query('body');

		body.classList.toggle(
			CSS.overflowHidden,
			query(`[${Attr.modalOpen}]`) != null
		);
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
		this.removeAttribute(Attr.modalOpen);
		this.toggleBodyOverflow();
		this.unlockNavigation();
		this.restoreInitialFormData();

		fire(EventName.modalClose, this);

		this.focusTrap.remove();

		setTimeout(() => {
			queryAll(`.${CSS.validate}`).forEach((input) => {
				input.classList.remove(CSS.validate);
			});
		}, 400);

		if (this.hasAttribute(Attr.destroy)) {
			setTimeout(() => {
				this.listeners.forEach(({ remove }) => {
					remove();
				});

				this.remove();
			}, 400);
		}
	}

	/**
	 * Open the modal.
	 */
	open(): void {
		this.setAttribute(Attr.modalOpen, '');
		this.toggleBodyOverflow();
		this.lockNavigation();
		this.saveInitialFormData();

		fire(EventName.modalOpen, this);

		this.focusTrap = createFocusTrap(this);
		this.addListener(this.focusTrap);

		if (!this.hasAttribute(Attr.noFocus)) {
			const input = query<InputElement>('input, textarea', this);

			if (input) {
				// Focus at end of input.
				const value = input.value;

				input.focus();
				input.value = '';
				input.value = value;
			}
		}
	}

	/**
	 * Save the initial form values on opening.
	 */
	protected saveInitialFormData(): void {
		this.formData = collectFieldData(this);
	}

	/**
	 * Restore the initial form data when modal was opened.
	 */
	protected restoreInitialFormData(): void {
		setFormData(this.formData, this);
	}
}

customElements.define(ModalComponent.TAG_NAME, ModalComponent);
