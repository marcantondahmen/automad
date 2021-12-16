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

import { listen, query } from '../utils/core';
import { BaseComponent } from './BaseComponent';

/**
 * <am-modal-toggle modal="#modal">
 *     Open
 * </am-modal-toggle>
 * <am-modal id="modal" nokey noclick>
 *     <am-modal-dialog>
 *         ...
 *     </am-modal-dialog>
 * </am-modal>
 */

class ModalToggle extends BaseComponent {
	static get observedAttributes() {
		return ['modal'];
	}

	connectedCallback() {
		const toggle = () => {
			const modal = query(this.elementAttributes.modal);

			modal.toggle();
		};

		listen(this, 'click', toggle.bind(this));
	}
}

class ModalClose extends BaseComponent {
	connectedCallback() {
		const close = () => {
			const modal = this.closest(`am-modal`);

			modal.close();
		};

		listen(this, 'click', close.bind(this));
	}
}

class Modal extends BaseComponent {
	static get observedAttributes() {
		return ['nokey', 'noclick'];
	}

	get useEsc() {
		return typeof this.elementAttributes.nokey === 'undefined';
	}

	get useClick() {
		return typeof this.elementAttributes.noclick === 'undefined';
	}

	get isOpen() {
		return this.matches(`.${this.cls.modalOpen}`);
	}

	connectedCallback() {
		if (this.useClick) {
			listen(this, 'click', (event) => {
				if (this === event.target) {
					this.close();
				}
			});
		}

		if (this.useEsc) {
			listen(window, 'keydown', (event) => {
				if (this.isOpen && event.keyCode == 27) {
					this.close();
				}
			});
		}
	}

	toggle() {
		if (this.isOpen) {
			this.close();
		} else {
			this.open();
		}
	}

	close() {
		this.classList.remove(this.cls.modalOpen);
		this.toggleBodyOverflow();
	}

	open() {
		this.classList.add(this.cls.modalOpen);
		this.toggleBodyOverflow();
	}

	toggleBodyOverflow() {
		const body = query('body');

		body.classList.toggle(
			this.cls.overflowHidden,
			query(`.${this.cls.modalOpen}`)
		);
	}
}

customElements.define('am-modal-toggle', ModalToggle);
customElements.define('am-modal-close', ModalClose);
customElements.define('am-modal', Modal);
