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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, create, CSS, html, listen } from '../../core';
import { ModalComponent } from './Modal';

/**
 * A wrapper element that allows for opening a field in modal mode.
 *
 * @extends BaseWindowComponent
 */
export class ModalFieldComponent extends ModalComponent {
	/**
	 * The tag name for the component.
	 *
	 * @static
	 */
	static TAG_NAME = 'am-modal-field';

	/**
	 * The class names for the component.
	 *
	 * @readonly
	 */
	protected readonly classes = [CSS.modalField];

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		super.connectedCallback();

		this.render();
	}

	/**
	 * Render the actual markup.
	 */
	private render(): void {
		const header = create('div', [CSS.modalFieldHeader], {}, this);
		const title = this.getAttribute(Attr.page) || App.text('sharedTitle');

		header.innerHTML = html`
			<am-icon-text
				${Attr.icon}="file-earmark-text"
				${Attr.text}="${title}"
			></am-icon-text>
			<span class="${CSS.modalFieldToggle}">
				<i class="bi bi-fullscreen-exit"></i>
			</span>
		`;

		create('div', [CSS.modalFieldToggle], {}, this).innerHTML = html`
			<i class="bi bi-window-fullscreen"></i>
		`;

		listen(
			this,
			'click',
			() => {
				this.toggle();
			},
			`.${CSS.modalFieldToggle}`
		);
	}
}

customElements.define(ModalFieldComponent.TAG_NAME, ModalFieldComponent);
