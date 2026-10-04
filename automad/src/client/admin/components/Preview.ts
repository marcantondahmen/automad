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
	CSS,
	getSlug,
	html,
	notifyInfo,
	routes,
} from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';
import { ModalComponent } from '@/admin/components/Modal/Modal';

const PREVIEW_ONLY_PARAM = 'preview-only';
const SCALES = [50, 75, 100] as const;
const NON_VISUAL_TAGS = ['SCRIPT', 'STYLE', 'LINK', 'TEMPLATE'];
const NOTIFICATION_DURATION = 3000;
const BLOCKED_CURSOR_CSS = `
	a[href]:not([href^="#"]),
	a[href]:not([href^="#"]) *,
	[type="submit"] {
		cursor: not-allowed !important;
	}
`;

/**
 * Get the initial scale based on the current window width.
 *
 * @returns 100 for windows wider than the threshold, else 75
 */
const getInitialScale = (): number => {
	return window.innerWidth > 1350 ? 100 : 75;
};

/**
 * Get the first opaque background color of the page's root, body or first
 * visible body child. That color is used to fill the area behind the iframe,
 * for example a stable scrollbar gutter of a page with a transparent canvas.
 *
 * @param doc
 * @returns the background color or null in case the page has no background
 */
const getPageBackground = (doc: Document | null): string | null => {
	const view = doc?.defaultView;

	if (!doc || !view || !doc.body) {
		return null;
	}

	const wrapper = Array.from(doc.body.children).find(
		(child) => !NON_VISUAL_TAGS.includes(child.tagName)
	);

	for (const element of [doc.documentElement, doc.body, wrapper]) {
		if (!element) {
			continue;
		}

		const color = view.getComputedStyle(element).backgroundColor;

		if (color && color !== 'transparent' && color !== 'rgba(0, 0, 0, 0)') {
			return color;
		}
	}

	return null;
};

/**
 * Prevent any navigation inside of the preview document, such as following
 * links (also to the dashboard) or submitting forms. Links to anchors on the
 * same page are still allowed. The user is informed about blocked navigation
 * using a notification and a "not-allowed" cursor on affected elements.
 *
 * @param doc
 */
const blockNavigation = (doc: Document | null): void => {
	if (!doc) {
		return;
	}

	let lastNotified = 0;

	const block = (event: Event): void => {
		const now = Date.now();

		event.preventDefault();

		// Avoid stacking notifications on repeated clicks.
		if (now - lastNotified > NOTIFICATION_DURATION) {
			lastNotified = now;
			notifyInfo(
				App.text('previewNavigationDisabled'),
				NOTIFICATION_DURATION
			);
		}
	};

	const blockLink = (event: Event): void => {
		const link = (event.target as Element).closest?.('a[href]');

		if (link && !link.getAttribute('href').startsWith('#')) {
			block(event);
		}
	};

	const style = doc.createElement('style');

	style.textContent = BLOCKED_CURSOR_CSS;
	doc.head?.appendChild(style);

	// Use the capture phase to run before any handler of the page itself.
	doc.addEventListener('click', blockLink, true);
	doc.addEventListener('auxclick', blockLink, true);
	doc.addEventListener('submit', block, true);
};

/**
 * A trigger component that opens a modal with a scaled preview of a public page.
 * The scale is controlled from within the modal. It initially is 100% in windows
 * wider than 1350px and 75% otherwise.
 *
 * @example
 * <am-preview ${Attr.url}="/path/to/page" ${Attr.text}="Page title">
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
		return [Attr.url, Attr.text];
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

			const modal = this.createModal(
				url,
				this.elementAttributes[Attr.text]
			);

			setTimeout(() => {
				modal.open();
			}, 0);
		});
	}

	/**
	 * Create the preview modal.
	 *
	 * @param url
	 * @param title
	 * @returns the created modal component
	 */
	private createModal(url: string, title: string): ModalComponent {
		const modal = create<ModalComponent>(
			'am-modal',
			[],
			{ [Attr.destroy]: '' },
			document.body
		);

		const dialog = create(
			'am-modal-dialog',
			[CSS.modalDialogExtraLarge, CSS.overflowHidden],
			{},
			modal
		);

		// The header is populated before it is connected, since the close button
		// is appended on connect and has to be the last item.
		const header = create('am-modal-header');
		const viewport = create('div', [CSS.previewViewport], {}, dialog);

		const frame = create<HTMLIFrameElement>(
			'iframe',
			[CSS.previewFrame],
			{ src: this.getPreviewSrc(url) },
			viewport
		);

		// Block navigation and match the page background. The latter falls back
		// to transparent in case the page background can't be read.
		this.listen(frame, 'load', () => {
			viewport.classList.add(CSS.previewViewportLoaded);

			try {
				blockNavigation(frame.contentDocument);

				viewport.style.backgroundColor =
					getPageBackground(frame.contentDocument) ?? '';
			} catch {
				viewport.style.backgroundColor = '';
			}
		});

		create(
			'am-icon-text',
			[],
			{
				[Attr.icon]: 'file-earmark-post',
				[Attr.text]: title || App.text('preview'),
			},
			header
		);

		const actions = create('div', [CSS.previewActions], {}, header);

		this.renderActions(actions, modal, viewport, url);
		dialog.prepend(header);

		this.setScale(viewport, getInitialScale());

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
		const scales = create(
			'div',
			[CSS.previewGroup, CSS.displaySmallNone],
			{},
			actions
		);

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
			html`<i class="bi bi-fullscreen"></i>`
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
