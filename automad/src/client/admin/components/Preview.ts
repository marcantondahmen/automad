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

import { App, Attr, create, CSS, getSlug, html, routes } from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';
import { ModalComponent } from '@/admin/components/Modal/Modal';

const PREVIEW_ONLY_PARAM = 'preview-only';
const SCALES = [50, 75, 100] as const;
const DEFAULT_SCALE = 50;

/**
 * A trigger component that opens a modal with a scaled preview of a public page.
 * The scale is controlled from within the modal and defaults to 50%.
 *
 * @example
 * <am-preview ${Attr.url}="/path/to/page">
 *     <i class="bi bi-eye"></i>
 * </am-preview>
 *
 * @extends BaseComponent
 */
class PreviewComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return [Attr.url];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.listen(this, 'click', () => {
			const url = this.elementAttributes[Attr.url];

			if (!url) {
				return;
			}

			const modal = this.createModal(url);

			setTimeout(() => {
				modal.open();
			}, 0);
		});
	}

	/**
	 * Create the preview modal.
	 *
	 * @param url
	 * @returns the created modal component
	 */
	private createModal(url: string): ModalComponent {
		const modal = create<ModalComponent>(
			'am-modal',
			[],
			{ [Attr.destroy]: '' },
			document.body
		);

		const dialog = create(
			'am-modal-dialog',
			[CSS.modalDialogLarge],
			{},
			modal
		);

		// The header is populated before it is connected, since the close button
		// is appended on connect and has to be the last item.
		const header = create('am-modal-header');
		const body = create('am-modal-body', [], {}, dialog);
		const viewport = create('div', [CSS.previewViewport], {}, body);

		create(
			'iframe',
			[CSS.previewFrame],
			{ src: this.getPreviewSrc(url) },
			viewport
		);

		create('span', [], {}, header, App.text('preview'));

		const actions = create('div', [CSS.previewActions], {}, header);

		this.renderActions(actions, modal, viewport, url);
		dialog.prepend(header);

		this.setScale(viewport, DEFAULT_SCALE);

		return modal;
	}

	/**
	 * Render the header actions.
	 *
	 * @param actions
	 * @param modal
	 * @param viewport
	 * @param url
	 */
	private renderActions(
		actions: HTMLElement,
		modal: ModalComponent,
		viewport: HTMLElement,
		url: string
	): void {
		const scales = create('div', [CSS.previewGroup]);

		SCALES.forEach((scale) => {
			const button = create(
				'span',
				[CSS.previewButton],
				{ 'data-scale': scale },
				scales,
				`${scale}%`
			);

			this.listen(button, 'click', () => {
				this.setScale(viewport, scale);
			});
		});

		create(
			'a',
			[CSS.previewButton],
			{
				href: `${App.baseIndex}${url}`,
				target: '_blank',
				[Attr.tooltip]: App.text('openInNewTab'),
			},
			actions,
			html`<i class="bi bi-box-arrow-up-right"></i>`
		);

		if (getSlug() !== routes.page) {
			const edit = create(
				'am-link',
				[CSS.previewButton],
				{
					[Attr.target]: `${routes.page}?url=${encodeURIComponent(url)}`,
					[Attr.tooltip]: App.text('edit'),
				},
				null,
				html`<i class="bi bi-pencil"></i>`
			);

			// This listener has to be registered before the link is connected,
			// since the modal has to release the navigation lock before the link
			// handles the click.
			this.listen(edit, 'click', () => {
				modal.close();
			});

			actions.appendChild(edit);
		}

		actions.appendChild(scales);
	}

	/**
	 * Get the iframe source that hides the in-page UI.
	 *
	 * @param url
	 * @returns the source URL
	 */
	private getPreviewSrc(url: string): string {
		const src = new URL(`${App.baseIndex}${url}`, window.location.origin);

		src.searchParams.set(PREVIEW_ONLY_PARAM, '1');

		return src.toString();
	}

	/**
	 * Apply a scale to the viewport and update the active button.
	 *
	 * @param viewport
	 * @param scale
	 */
	private setScale(viewport: HTMLElement, scale: number): void {
		viewport.style.setProperty('--am-preview-scale', `${scale / 100}`);

		const modal = viewport.closest('am-modal');

		modal
			?.querySelectorAll(`.${CSS.previewButton}[data-scale]`)
			.forEach((button) => {
				button.classList.toggle(
					CSS.previewButtonActive,
					button.getAttribute('data-scale') === `${scale}`
				);
			});
	}
}

customElements.define('am-preview', PreviewComponent);
