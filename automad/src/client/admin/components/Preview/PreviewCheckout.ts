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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import {
	App,
	Attr,
	create,
	createSelect,
	CSS,
	dateFormat,
	getPageURL,
	HistoryController,
	requestApi,
} from '@/admin/core';
import { BasePreviewComponent } from './BasePreview';
import { ModalComponent } from '../Modal/Modal';
import { SelectComponentOption } from '../Select';

const REVISION_PARAM = 'revision';

/**
 * A trigger component that opens a modal with a scaled preview of a revision.
 *
 * @example
 * <am-preview-checkout ${Attr.url}="/path/to/page" ${Attr.text}="Page title">
 *     <i class="bi bi-eye"></i>
 * </am-preview-checkout>
 *
 * @extends BasePreviewComponent
 */
class PreviewCheckoutComponent extends BasePreviewComponent {
	/**
	 * Initialize the preview.
	 *
	 * @param url
	 * @param modal
	 * @param actions
	 * @param viewport
	 * @param frame
	 * @async
	 */
	protected async init(
		url: string,
		modal: ModalComponent,
		actions: HTMLElement,
		viewport: HTMLElement,
		frame: HTMLIFrameElement
	): Promise<void> {
		const { data } = await requestApi(HistoryController.log, {
			url: getPageURL(),
		});

		if (!data) {
			const alert = create(
				'div',
				[CSS.previewViewport, CSS.previewViewportLoaded],
				{}
			);

			create(
				'am-alert',
				[],
				{
					[Attr.icon]: 'slash-circle',
					[Attr.text]: 'pageHistoryNoRevision',
				},
				alert
			);

			viewport.replaceWith(alert);

			return;
		}

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

		const form = create(
			'am-form',
			[CSS.formGroup],
			{
				[Attr.api]: HistoryController.restore,
				[Attr.confirm]: App.text('pageHistoryRestoreConfirm'),
			},
			actions
		);

		const revisionSelect = createSelect(
			options,
			options[0].value,
			form,
			'revision',
			null,
			null,
			[CSS.formGroupItem]
		);

		create(
			'am-submit',
			[CSS.formGroupItem, CSS.button],
			{ [Attr.tooltip]: App.text('pageHistoryRestoreTooltip') },
			form,
			App.text('pageHistoryRestore')
		);

		const setSrc = (): void => {
			viewport.classList.remove(CSS.previewViewportLoaded);

			const src = new URL(
				`${App.baseIndex}${url}`,
				window.location.origin
			);

			src.searchParams.set(REVISION_PARAM, revisionSelect.value);
			frame.src = src.toString();
		};

		modal.listen(revisionSelect.select, 'change', setSrc);

		setSrc();
	}

	/**
	 * The header icon.
	 *
	 * @returns the icon name
	 */
	protected getIcon(): string {
		return 'clock-history';
	}
}

customElements.define('am-preview-checkout', PreviewCheckoutComponent);
