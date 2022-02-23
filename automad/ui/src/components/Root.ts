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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	classes,
	App,
	getValidRouteOrRedirect,
	create,
	getTagFromRoute,
	waitForPendingRequests,
	query,
	listen,
	queryAll,
} from '../core';
import { UIState } from '../types';
import { BaseComponent } from './Base';
import { FormComponent } from './Forms/Form';

/**
 * The root app component.
 *
 * @extends BaseComponent
 */
export class RootComponent extends BaseComponent {
	/**
	 * The main child of the root element that is basically a rendered view.
	 */
	private node: HTMLElement;

	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['base'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.init();
	}

	/**
	 * Set a new URL and update the view accordingly.
	 *
	 * @param url
	 * @param useMainState
	 */
	setView(url: URL, useMainState: boolean = false): void {
		if (this.confirmViewUpdate()) {
			window.history.pushState(null, null, url);
			this.update(this.saveUIState(useMainState));
		}
	}

	/**
	 * Init the root component.
	 *
	 * @async
	 */
	private async init(): Promise<void> {
		this.classList.add(classes.root);
		this.progressBar(10);

		await App.bootstrap(this);

		this.update();

		listen(window, 'popstate', () => {
			App.root.update();
		});
	}

	/**
	 * Confirm updating the view in case a watched form has unsaved changes.
	 *
	 * @returns true in case the user confirms to update the view without saving changes.
	 */
	private confirmViewUpdate(): boolean {
		const forms = queryAll('[watch]');
		const message = App.text('confirmDiscardUnsaved');
		let hasChanges = false;
		let confirmed = false;

		forms.forEach((form: FormComponent) => {
			if (form.hasUnsavedChanges) {
				hasChanges = true;
			}
		});

		if (!(hasChanges && !window.confirm(message))) {
			confirmed = true;

			// Reset the unsaved changes status property in order to
			// avoid accumulation beforeunload event handlers.
			forms.forEach((form: FormComponent) => {
				form.hasUnsavedChanges = false;
			});
		}

		return confirmed;
	}

	/**
	 * Update the root component.
	 *
	 * @param uiState
	 * @async
	 */
	private async update(uiState: UIState = null): Promise<void> {
		this.progressBar(25);

		await App.updateState();

		this.progressBar(50);

		const route = getValidRouteOrRedirect();
		const page = create(getTagFromRoute(route), [], {}).init();

		this.progressBar(70);

		await waitForPendingRequests();

		if (typeof this.node !== 'undefined') {
			this.node.replaceWith(page);
			document.body.removeAttribute('class');
		} else {
			this.appendChild(page);
		}

		this.node = page;

		this.progressBar(100);

		await waitForPendingRequests();

		setTimeout(async () => {
			this.progressBar(0);
			this.applyUIState(uiState);
		}, 0);
	}

	/**
	 * Control the progress bar display.
	 *
	 * @param progress
	 */
	private progressBar(progress: number): void {
		this.classList.toggle(classes.rootLoading, progress > 0);

		if (progress > 0) {
			this.style.setProperty('--progress', `${progress}%`);

			return;
		}

		setTimeout(() => {
			this.style.setProperty('--progress', `${progress}%`);
		}, 0);
	}

	/**
	 * Cache the UI state before changing the view.
	 *
	 * @param useMainState
	 */
	private saveUIState(useMainState: boolean): UIState {
		const sidebar = query('am-sidebar');
		const sidebarScroll = sidebar?.scrollTop;

		const uiState: UIState = {
			sidebarScroll,
			documentScroll: 0,
		};

		if (useMainState) {
			const documentScroll = query('html').scrollTop;
			const focused = document.activeElement as HTMLInputElement;

			let focusedId = null;
			let focusedCursorPosition = null;

			if (focused) {
				focusedId = focused.getAttribute('id');

				try {
					focusedCursorPosition = focused.selectionStart;
				} catch {}
			}

			uiState.documentScroll = documentScroll;
			uiState.focusedId = focusedId;
			uiState.focusedCursorPosition = focusedCursorPosition;
		}

		return uiState;
	}

	/**
	 * Load the UI state after changing the view.
	 *
	 * @param uiState
	 */
	private applyUIState(uiState: UIState): void {
		if (!uiState) {
			return;
		}

		const sidebar = query('am-sidebar');
		const {
			documentScroll,
			sidebarScroll,
			focusedId,
			focusedCursorPosition,
		} = uiState;

		if (sidebar) {
			sidebar.scrollTop = sidebarScroll;
		}

		query('html').scrollTop = documentScroll;

		if (focusedId) {
			const focused = document.getElementById(
				focusedId
			) as HTMLInputElement;

			if (focused) {
				focused.focus();

				try {
					focused.setSelectionRange(
						focusedCursorPosition,
						focusedCursorPosition
					);
				} catch {}
			}
		}
	}
}

customElements.define('am-root', RootComponent);
