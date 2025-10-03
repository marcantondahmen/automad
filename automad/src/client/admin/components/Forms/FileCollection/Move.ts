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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	collectFieldData,
	createGenericModal,
	CSS,
	getPageURL,
	html,
	query,
} from '@/admin/core';
import { FormComponent } from '@/admin/components/Forms/Form';
import { BaseFileCollectionSubmitComponent } from './BaseSubmit';

/**
 * A move button for the FileCollectionListComponent form.

 * @extends BaseFileCollectionSubmitComponent
 */
class FileCollectionMoveComponent extends BaseFileCollectionSubmitComponent {
	/**
	 * The actual submit implementation for moving files.
	 */
	async submit(): Promise<void> {
		if (this.hasAttribute('disabled')) {
			return;
		}

		const { modal, body, button } = createGenericModal(
			App.text('selectTargetMovePage'),
			App.text('ok')
		);

		body.innerHTML = html`
			<label class="${CSS.navItem}">
				<input type="radio" name="targetPage" value="" tabindex="0" />
				<span class="${CSS.navLink}">
					<am-icon-text
						${Attr.icon}="folder2"
						${Attr.text}="${App.text('sharedTitle')}"
					></am-icon-text>
				</span>
			</label>
			<hr />
			<am-page-select-tree></am-page-select-tree>
		`;

		let target = getPageURL();

		this.listen(body, 'change', () => {
			const { targetPage } = collectFieldData(body);

			target = targetPage;
		});

		this.listen(button, 'click', () => {
			this.relatedForms.forEach((form: FormComponent) => {
				form.additionalData = { action: 'move', target };
				form.submit();
			});
		});

		modal.setAttribute(Attr.noFocus, '');
		modal.open();

		setTimeout(() => {
			query(':focus', body)?.blur();
		}, 0);
	}
}

customElements.define('am-file-collection-move', FileCollectionMoveComponent);
