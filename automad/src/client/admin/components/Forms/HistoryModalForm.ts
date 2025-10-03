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

import {
	App,
	EventName,
	create,
	CSS,
	html,
	query,
	requestAPI,
	getPageURL,
	createSelect,
	Attr,
	HistoryController,
	dateFormat,
} from '@/admin/core';
import { SelectComponentOption } from '@/admin/types';
import { BaseComponent } from '@/admin/components/Base';
import { ModalComponent } from '@/admin/components/Modal/Modal';

export class HistoryModalFormComponent extends BaseComponent {
	/**
	 * The modal id.
	 *
	 * @static
	 */
	static MODAL_ID = 'am-history-modal';

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		let confirm = '';

		if (getPageURL() == '/') {
			confirm = `${Attr.confirm}="${App.text(
				'pageHistoryRestoreHomeConfirm'
			)}"`;
		}

		const modal = create(
			ModalComponent.TAG_NAME,
			[],
			{ id: HistoryModalFormComponent.MODAL_ID },
			this,
			html`
				<am-form
					class="${CSS.modalDialog}"
					${Attr.api}="${HistoryController.restore}"
					${confirm}
				>
					<am-modal-header>
						${App.text('pageHistory')}
					</am-modal-header>
					<am-modal-body class="${CSS.flexGapLarge}"></am-modal-body>
					<am-modal-footer>
						<am-modal-close class="${CSS.button}"
							>${App.text('cancel')}</am-modal-close
						>
						<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
							${App.text('pageHistoryRestore')}
						</am-submit>
					</am-modal-footer>
				</am-form>
			`
		);

		const body = query('am-modal-body', modal);

		this.listen(modal, EventName.modalOpen, () => {
			this.init(body);
		});
	}

	/**
	 * Init the form.
	 *
	 * @param container
	 * @async
	 */
	private async init(container: HTMLElement): Promise<void> {
		container.innerHTML = '<am-spinner></am-spinner>';

		const { data } = await requestAPI(HistoryController.log, {
			url: getPageURL(),
		});

		if (!data) {
			container.innerHTML = App.text('pageHistoryNoRevision');

			return;
		}

		container.innerHTML = `<span>${
			getPageURL() == '/'
				? App.text('pageHistoryRestoreHomeText')
				: App.text('pageHistoryRestoreText')
		}</span>`;

		const options = data.reduce(
			(
				res: SelectComponentOption[],
				rev: { time: string; hash: string }
			) => {
				const text = dateFormat(rev.time);

				res.push({ text, value: rev.hash });

				return res;
			},
			[]
		);

		createSelect(options, options[0].value, container, 'revision');
	}
}

customElements.define('am-history-modal-form', HistoryModalFormComponent);
