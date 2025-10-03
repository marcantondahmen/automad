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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, create, CSS, html, queryAll } from '@/admin/core';
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

		// Make sure all fields that are clicked have a higher z-index
		// in order to keep menues and toolbars of those fiels on top even
		// when overlapping with fields below.
		this.listen(this, 'click', () => {
			const modalFields = queryAll<ModalFieldComponent>(
				ModalFieldComponent.TAG_NAME
			);

			modalFields.forEach((field) => {
				field.removeAttribute('style');
			});

			this.style.zIndex = '40';
		});
	}

	/**
	 * Render the actual markup.
	 */
	private render(): void {
		const title = this.getAttribute(Attr.page) || App.text('sharedTitle');

		create(
			'div',
			[CSS.modalFieldHeader],
			{},
			this,
			html`
				<am-icon-text
					${Attr.icon}="file-earmark-text"
					${Attr.text}="${title}"
				></am-icon-text>
				<span class="${CSS.modalFieldToggle}">↙</span>
			`
		);

		create(
			'div',
			[CSS.modalFieldToggle, CSS.displaySmallNone],
			{},
			this
		).innerHTML = '↗';

		this.listen(
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
