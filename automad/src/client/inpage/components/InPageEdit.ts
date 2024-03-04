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
 * Copyright (c) 2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { create } from '@/common';
import { saveScrollPosition } from '../sessionStore';
import { BaseInPageComponent } from './BaseInPageComponent';

/**
 * The InPage edit button component.
 */
export class InPageEditComponent extends BaseInPageComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add('am-inpage-edit');

		const page = this.getAttr('page');
		const dashboard = this.getAttr('dashboard');
		const context = this.getAttr('context');
		const label = this.getAttr('label');

		// Keep attribute field and therefore don't use getAttr().
		// The field attribute is used to determine if the field contains blocks or not
		// in order to apply correct styles.
		const field = this.getAttribute('field');

		const overlay = create('span', ['am-inpage-edit__overlay'], {}, this);

		const button = create(
			'span',
			['am-inpage-edit__button'],
			{},
			overlay,
			label
		);

		button.addEventListener('click', () => {
			const query = new URLSearchParams({
				field,
				page,
				context,
			}).toString();

			saveScrollPosition();

			window.location.href = `${dashboard}/inpage?${query}`;
		});
	}
}

customElements.define('am-inpage-edit', InPageEditComponent);
