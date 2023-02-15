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

import { Attr, CSS, listen } from '../../core';
import { BaseComponent } from '../Base';

/**
 * The base modal component.
 * The following attributes can be added to a modal component:
 * - Attr.noEsc - Disable the ESC key
 * - Attr.noClick - Disable closing the modal by clicking on the overlay
 *
 * @extends BaseComponent
 */
export abstract class BaseWindowComponent extends BaseComponent {
	/**
	 * The class name for the component.
	 *
	 * @readonly
	 */
	protected readonly classes = {
		modal: CSS.modal,
		open: CSS.modalOpen,
	};

	/**
	 * True if the modal dialog is open.
	 */
	get isOpen(): boolean {
		return this.matches(`.${this.classes.open}`);
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(this.classes.modal);

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
	 * Close the modal.
	 */
	close(): void {
		this.classList.remove(this.classes.open);
	}

	/**
	 * Open the modal.
	 */
	open(): void {
		this.classList.add(this.classes.open);
	}
}
