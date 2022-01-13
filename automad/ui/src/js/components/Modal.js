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
import { BaseComponent } from './BaseComponent';

/**
 * A modal component.
 * The following attributes can be added to a modal component:
 * - `noesc` - Disable the ESC key
 * - `noclick` - Disable closing the modal by clicking on the overlay
 *
 * ```
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
 * ```
 *
 * @extends BaseComponent
 */
class Modal extends BaseComponent {
	/**
	 * True if the modal dialog is open.
	 *
	 * @type {boolean}
	 */
	get isOpen() {
		return this.matches(`.${classes.modalOpen}`);
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		this.classList.add(classes.modal);

		if (!this.hasAttribute('noclick')) {
			listen(this, 'click', (event) => {
				if (this === event.target) {
					this.close();
				}
			});
		}

		if (!this.hasAttribute('noesc')) {
			listen(window, 'keydown', (event) => {
				if (this.isOpen && event.keyCode == 27) {
					this.close();
				}
			});
		}
	}

	/**
	 * Toggle the modal.
	 */
	toggle() {
		if (this.isOpen) {
			this.close();
		} else {
			this.open();
		}
	}

	/**
	 * Close the modal.
	 */
	close() {
		this.classList.remove(classes.modalOpen);
		this.toggleBodyOverflow();
	}

	/**
	 * Open the modal.
	 */
	open() {
		this.classList.add(classes.modalOpen);
		this.toggleBodyOverflow();
	}

	/**
	 * Toggle the body overflow class when the modal is open.
	 */
	toggleBodyOverflow() {
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
 * @see {@link Modal}
 * @extends BaseComponent
 */
class ModalToggle extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @type {Array}
	 * @static
	 */
	static get observedAttributes() {
		return ['modal'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		const toggle = () => {
			const modal = query(this.elementAttributes.modal);

			modal.toggle();
		};

		listen(this, 'click', toggle.bind(this));
	}
}

/**
 * A modal close button that is placed inside the modal dialog.
 *
 * @see {@link Modal}
 * @extends BaseComponent
 */
class ModalClose extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		const close = () => {
			const modal = this.closest('am-modal');

			if (modal instanceof Modal) {
				modal.close();
			}
		};

		listen(this, 'click', close.bind(this));
	}
}

customElements.define('am-modal', Modal);
customElements.define('am-modal-toggle', ModalToggle);
customElements.define('am-modal-close', ModalClose);
