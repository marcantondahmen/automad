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

import { classes, listen, query } from '../utils/core';
import { BaseComponent } from './Base';

/**
 * A modal component.
 * The following attributes can be added to a modal component:
 * - `noesc` - Disable the ESC key
 * - `noclick` - Disable closing the modal by clicking on the overlay
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
class ModalComponent extends BaseComponent {
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
	}

	/**
	 * Open the modal.
	 */
	open(): void {
		this.classList.add(classes.modalOpen);
		this.toggleBodyOverflow();
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
}

/**
 * A modal toggle button.
 *
 * @see {@link ModalComponent}
 * @extends BaseComponent
 */
class ModalToggleComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['modal'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const toggle = () => {
			const modal = query(this.elementAttributes.modal) as ModalComponent;

			modal.toggle();
		};

		listen(this, 'click', toggle.bind(this));
	}
}

/**
 * A modal close button that is placed inside the modal dialog.
 *
 * @see {@link ModalComponent}
 * @extends BaseComponent
 */
class ModalCloseComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		const close = () => {
			const modal = this.closest('am-modal');

			if (modal instanceof ModalComponent) {
				modal.close();
			}
		};

		listen(this, 'click', close.bind(this));
	}
}

customElements.define('am-modal', ModalComponent);
customElements.define('am-modal-toggle', ModalToggleComponent);
customElements.define('am-modal-close', ModalCloseComponent);
