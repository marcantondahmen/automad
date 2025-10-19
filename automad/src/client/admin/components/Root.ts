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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	CSS,
	App,
	getValidRouteOrRedirect,
	create,
	getTagFromRoute,
	waitForPendingRequests,
	Bindings,
	initCheckboxToggles,
	queryAll,
	initTooltips,
	requestAPI,
	Attr,
	initEnterKeyHandler,
	initWindowErrorHandler,
	fire,
	EventName,
	Undo,
	SessionController,
	initInputChangeHandler,
	applyTheme,
	getTheme,
	listen,
} from '@/admin/core';
import { BaseComponent } from '@/admin/components/Base';
import { ModalComponent } from './Modal/Modal';
import { SidebarComponent } from './Sidebar/Sidebar';

/**
 * The root app component.
 *
 * @extends BaseComponent
 */
export class RootComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['base-index', 'base-url'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.init();
	}

	/**
	 * Set a new URL and update the view accordingly.
	 * Note that an empty string can be used ro rerender a view.
	 *
	 * @param url
	 */
	setView(url: URL): void {
		SidebarComponent.toggle(false);

		if (url) {
			window.history.pushState(null, null, url);
		}

		this.update();
	}

	/**
	 * Init the root component.
	 *
	 * @async
	 */
	private async init(): Promise<void> {
		this.classList.add(CSS.root);
		initWindowErrorHandler();
		applyTheme(getTheme());
		this.progressBar(10);

		await App.bootstrap(this);
		await this.update();

		listen(window, 'popstate', () => {
			App.root.update();
		});

		initEnterKeyHandler();
		initInputChangeHandler();
		this.validateSession();

		Undo.addListeners();
	}

	/**
	 * Update the root component.
	 *
	 * @async
	 */
	private async update(): Promise<void> {
		fire(EventName.beforeUpdateView, window);

		const openModal = queryAll<ModalComponent>(`[${Attr.modalOpen}]`);

		if (openModal) {
			openModal.forEach((modal) => {
				modal.close();
			});
			await new Promise((resolve) => setTimeout(resolve, 250));
		}

		this.progressBar(20);
		this.removeListeners();

		App.isReady = false;

		initTooltips();
		Bindings.reset();
		Undo.new();

		await App.updateState();

		this.progressBar(40);

		const route = getValidRouteOrRedirect();

		if (!route) {
			return;
		}

		const page = create(getTagFromRoute(route), [], {}).init();

		this.progressBar(60);

		await waitForPendingRequests();

		this.progressBar(80);

		this.innerHTML = '';
		this.appendChild(page);

		await waitForPendingRequests();

		Bindings.connectElements(this);
		initCheckboxToggles(this);

		App.checkForSystemUpdate();
		App.checkForOutdatedPackages();
		App.restoreFilterAndScroll();

		this.progressBar(100);

		App.isReady = true;
	}

	/**
	 * Verify the CSRF token on visiblity state change (change tab) in order
	 * to make sure that the token is updated also between mutliple sessions while a tab is still open.
	 *
	 * @async
	 */
	private async validateSession(): Promise<void> {
		const stateChangeHandler = async (): Promise<void> => {
			if (document.visibilityState === 'visible') {
				const data = await requestAPI(SessionController.validate, {
					// Send a random key/value pair in order to provide a valid POST request.
					csrfTokenValidation: 1,
				});

				const code = data.code || 403;

				if (code === 403) {
					App.reload();
				}

				if (data.redirect) {
					this.setView(data.redirect);
				}
			}
		};

		listen(document, 'visibilitychange', stateChangeHandler.bind(this));
		listen(window, 'focus', stateChangeHandler.bind(this));
	}

	/**
	 * Control the progress bar display.
	 *
	 * @param progress
	 */
	private progressBar(progress: number): void {
		this.classList.toggle(CSS.rootLoading, progress > 0 && progress < 100);

		if (progress > 0) {
			this.style.setProperty('--progress', `${progress}%`);

			return;
		}

		setTimeout(() => {
			this.style.setProperty('--progress', `${progress}%`);
		}, 0);
	}
}

customElements.define('am-root', RootComponent);
