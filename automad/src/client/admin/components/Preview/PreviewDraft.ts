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
	Bindings,
	create,
	CSS,
	DraftCollectionController,
	EventName,
	getSlug,
	PageController,
	routes,
} from '@/admin/core';
import { BasePreviewComponent } from './BasePreview';
import { ModalComponent } from '../Modal/Modal';
import { FormComponent } from '../Forms/Form';

const PREVIEW_ONLY_PARAM = 'preview-only';

/**
 * A trigger component that opens a modal with a scaled preview of a public page.
 * The scale is controlled from within the modal. It initially is 100% in windows
 * wider than 1350px and 75% otherwise.
 *
 * @example
 * <am-preview-draft ${Attr.url}="/path/to/page" ${Attr.text}="Page title">
 *     <i class="bi bi-eye"></i>
 * </am-preview-draft>
 *
 * @extends BasePreviewComponent
 */
class PreviewDraftComponent extends BasePreviewComponent {
	/**
	 * Get the iframe source that hides the in-page UI.
	 *
	 * @param url
	 * @returns the source URL
	 */
	protected getPreviewSrc(url: string): string {
		const src = new URL(`${App.baseIndex}${url}`, window.location.origin);

		src.searchParams.set(PREVIEW_ONLY_PARAM, '1');

		return src.toString();
	}

	/**
	 * Render the header actions.
	 *
	 * @param actions
	 * @param modal
	 * @param frame
	 * @param url
	 * @async
	 */
	protected async renderActions(
		actions: HTMLElement,
		modal: ModalComponent,
		frame: HTMLIFrameElement,
		url: string
	): Promise<void> {
		create(
			'a',
			[CSS.previewButton],
			{
				href: `${App.baseIndex}${url}`,
				target: '_blank',
				[Attr.tooltip]: App.text('openInNewTab'),
			},
			actions,
			'<i class="bi bi-box-arrow-up-right"></i>'
		);

		const isPageRoute = getSlug() === routes.page;

		if (!isPageRoute) {
			const edit = create(
				'am-link',
				[CSS.previewButton],
				{
					[Attr.target]: `${routes.page}?url=${encodeURIComponent(url)}`,
					[Attr.tooltip]: App.text('edit'),
				},
				null,
				'<i class="bi bi-pencil-fill"></i>'
			);

			// This listener has to be registered before the link is connected,
			// since the modal has to release the navigation lock before the link
			// handles the click.
			modal.listen(edit, 'click', () => {
				modal.close();
			});

			actions.appendChild(edit);
		}

		// The form closes the modal after the page has been published successfully.
		const publishForm = create<FormComponent>(
			'am-form',
			[],
			{
				[Attr.api]: isPageRoute
					? PageController.publish
					: DraftCollectionController.publishPage,
				[Attr.event]: isPageRoute
					? EventName.contentPublished
					: EventName.appStateRequireUpdate,
			},
			actions
		);

		publishForm.additionalData = { url };

		const publish = create(
			'am-submit',
			[CSS.previewButton],
			{ [Attr.tooltip]: App.text('publish') },
			publishForm,
			'<i class="bi bi-check-circle"></i>'
		);

		if (
			isPageRoute &&
			Bindings.get('publicationState')?.value === 'published'
		) {
			publish.setAttribute('disabled', '');
		}
	}
}

customElements.define('am-preview-draft', PreviewDraftComponent);
