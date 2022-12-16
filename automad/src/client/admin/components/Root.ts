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
 * Copyright (c) 2021-2022 by Marc Anton Dahmen
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
	listen,
	Bindings,
	initCheckboxToggles,
	queryAll,
	initTooltips,
	requestAPI,
} from '../core';
import { applyTheme, getTheme } from '../core/theme';
import { BaseComponent } from './Base';
import { ModalComponent } from './Modal/Modal';

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
	 */
	setView(url: URL): void {
		window.history.pushState(null, null, url);
		this.update();
	}

	/**
	 * Init the root component.
	 *
	 * @async
	 */
	private async init(): Promise<void> {
		this.classList.add(CSS.root);
		applyTheme(getTheme());
		this.progressBar(10);

		this.initMissingImageErrorHandler();

		await App.bootstrap(this);
		await this.update();

		initTooltips();

		this.listeners.push(
			listen(window, 'popstate', () => {
				App.root.update();
			})
		);

		this.validateSession();
	}

	/**
	 * Update the root component.
	 *
	 * @async
	 */
	private async update(): Promise<void> {
		const openModal = queryAll(`.${CSS.modalOpen}`) as ModalComponent[];

		if (openModal) {
			openModal.forEach((modal) => {
				modal.close();
			});
			await new Promise((resolve) => setTimeout(resolve, 250));
		}

		this.progressBar(20);

		Bindings.reset();

		await App.updateState();

		this.progressBar(40);

		const route = getValidRouteOrRedirect();
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

		this.progressBar(100);
	}

	/**
	 * Verify the app id (browser tab id) and the CSRF token on visiblity state change (change tab) in order
	 * to make sure that the token is updated also between mutliple sessions while a tab is still open.
	 *
	 * @async
	 */
	private async validateSession(): Promise<void> {
		const bodyScrollYKey = 'bodyScrollY';
		const bodyScrollY = localStorage.getItem(bodyScrollYKey);
		const stateChangeHandler = async (): Promise<void> => {
			if (document.visibilityState === 'visible') {
				const data = await requestAPI('Session/validate', {
					// Send a random key/value pair in order to provide a valid POST request.
					csrfTokenValidation: 1,
				});

				const code = data.code || 403;

				if (code === 403) {
					localStorage.setItem(
						bodyScrollYKey,
						String(window.scrollY)
					);

					window.location.reload();
				}

				if (data.redirect) {
					this.setView(data.redirect);
				}
			}
		};

		if (bodyScrollY) {
			window.scrollTo(0, parseInt(bodyScrollY));
			localStorage.removeItem(bodyScrollYKey);
		}

		this.listeners.push(
			listen(document, 'visibilitychange', stateChangeHandler.bind(this))
		);

		this.listeners.push(
			listen(window, 'focus', stateChangeHandler.bind(this))
		);
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

	/**
	 * Detect missing images and replace them with an icon.
	 */
	private initMissingImageErrorHandler(): void {
		this.addEventListener(
			'error',
			(event: Event) => {
				const target = event.target as HTMLElement;

				if (target.tagName.toLowerCase() === 'img') {
					target.replaceWith(create('i', ['bi', 'bi-slash-circle']));
				}
			},
			true
		);
	}
}

customElements.define('am-root', RootComponent);
