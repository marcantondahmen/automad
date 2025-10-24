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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, create, keyCombo } from '@/admin/core';
import { ModalComponent } from './Modal';

/**
 * A modal jumpbar.
 *
 * @see {@link ModalComponent}
 * @extends ModalComponent
 */
class ModalJumpbarComponent extends ModalComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		super.connectedCallback();

		create(
			'am-modal-jumpbar-dialog',
			[],
			{ placeholder: 'jumpbarButtonText' },
			this
		);

		this.addListener(
			keyCombo('j', () => {
				if (App.navigationIsLocked) {
					return;
				}

				this.open();
			})
		);

		this.listen(
			this,
			'focusout',
			() => {
				this.close();
			},
			'input'
		);
	}

	/**
	 * Lock the navigation.
	 */
	protected lockNavigation(): void {}

	/**
	 * Unlock the navigation.
	 */
	protected unlockNavigation(): void {}

	/**
	 * Save the initial form values on opening.
	 */
	protected saveInitialFormData(): void {}

	/**
	 * Restore the initial form data when modal was opened.
	 */
	protected restoreInitialFormData(): void {}
}

customElements.define('am-modal-jumpbar', ModalJumpbarComponent);
