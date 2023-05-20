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
 * Copyright (c) 2022-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { RootComponent } from '@/components/Root';
import {
	deleteSearchParam,
	EventName,
	fire,
	getSearchParam,
	listen,
	notifySuccess,
	query,
	remoteTrigger,
	request,
	requestAPI,
	setSearchParam,
	State,
} from '.';
import {
	InputElement,
	KeyValueMap,
	Pages,
	SystemSettings,
	ThemeCollection,
	User,
} from '@/types';

/**
 * The static class that provides the app state and root element to be used across the application.
 */
export class App {
	/**
	 * Get a state property.
	 *
	 * @static
	 * @param key
	 * @return the property value
	 */
	private static getState(
		key: keyof KeyValueMap
	): KeyValueMap[keyof KeyValueMap] {
		return State.getInstance().get(key);
	}

	/**
	 * The internal state of the nav.
	 *
	 * @static
	 */
	private static navigationLocks: KeyValueMap = {};

	/**
	 * Internal lock id counter.
	 *
	 * @static
	 */
	private static latestLockId: number = 0;

	/**
	 * The API base url.
	 *
	 * @static
	 */
	static apiURL = '';

	/**
	 * The base URL for the website.
	 *
	 * @static
	 */
	static baseURL = '';

	/**
	 * The app initialization state.
	 *
	 * @static
	 */
	static isReady = false;

	/**
	 * The array of allowed file types.
	 *
	 * @static
	 */
	static get allowedFileTypes(): string[] {
		return App.getState('allowedFileTypes');
	}

	/**
	 * The dashboard URL.
	 *
	 * @static
	 */
	static get dashboardURL(): string {
		return App.getState('dashboard');
	}

	/**
	 * The feed URL.
	 *
	 * @static
	 */
	static get feedURL(): string {
		return App.getState('feed');
	}

	/**
	 * The languages map.
	 *
	 * @static
	 */
	static get languages(): KeyValueMap {
		return App.getState('languages');
	}

	/**
	 * The main theme path.
	 *
	 * @static
	 */
	static get mainTheme(): string {
		return App.getState('mainTheme');
	}

	/**
	 * The package repo api endpoint.
	 */
	static get packageRepo(): string {
		return App.getState('packageRepo');
	}

	/**
	 * The pages array used to build the nav tree.
	 *
	 * @static
	 */
	static get pages(): Pages {
		return App.getState('pages');
	}

	/**
	 * The map of reserved field names.
	 *
	 * @static
	 */
	static get reservedFields(): KeyValueMap {
		return App.getState('reservedFields');
	}

	/**
	 * The array of content field names.
	 *
	 * @static
	 */
	static get contentFields(): string[] {
		return App.getState('contentFields');
	}

	/**
	 * The name of the site.
	 *
	 * @static
	 */
	static get sitename(): string {
		return App.getState('sitename');
	}

	/**
	 * The array of tags that are used across the site.
	 *
	 * @static
	 */
	static get tags(): string[] {
		return App.getState('tags');
	}

	/**
	 * The array of installed themes.
	 *
	 * @static
	 */
	static get themes(): ThemeCollection {
		return App.getState('themes');
	}

	/**
	 * The system settings.
	 *
	 * @static
	 */
	static get system(): SystemSettings {
		return App.getState('system');
	}

	/**
	 * The current user.
	 *
	 * @static
	 */
	static get user(): User {
		return App.getState('user');
	}

	/**
	 * The state.
	 *
	 * @static
	 */
	static get state(): KeyValueMap {
		return State.getInstance().data;
	}

	/**
	 * The root element.
	 *
	 * @static
	 */
	static get root(): RootComponent {
		return State.getInstance().root;
	}

	/**
	 * True if the nav is blocked.
	 *
	 * @static
	 */
	static get navigationIsLocked() {
		return Object.keys(this.navigationLocks).length > 0;
	}

	/**
	 * The Automad version.
	 *
	 * @static
	 */
	static get version() {
		return App.getState('version');
	}

	/**
	 * The bootstrap method that requested the basic state data.
	 *
	 * @static
	 * @async
	 * @param root
	 */
	static async bootstrap(root: RootComponent): Promise<void> {
		App.baseURL = root.elementAttributes.base;
		App.apiURL = `${App.baseURL}/api`;

		const { data } = await requestAPI(`App/bootstrap`);
		const state = State.getInstance();

		state.bootstrap(root, data);

		listen(
			window,
			EventName.appStateRequireUpdate,
			this.updateState.bind(this)
		);

		setTimeout(async () => {
			const { success } = await requestAPI('App/getLanguagePacks');

			if (success) {
				notifySuccess(success, -1);
			}

			remoteTrigger('PackageManager/preFetchThumbnails');
		}, 2000);
	}

	/**
	 * Update the state according to a change of view.
	 *
	 * @async
	 * @static
	 */
	static async updateState(): Promise<void> {
		const response = await requestAPI('App/updateState', null, false);
		const state = State.getInstance();

		state.update(response.data);
	}

	/**
	 * Set the nav state to be disabled.
	 *
	 * @static
	 * @return the lock id
	 */
	static addNavigationLock(): number {
		const id = this.latestLockId++;

		this.navigationLocks[id] = true;

		return id;
	}

	/**
	 * Save the current filter and window scroll states to the query string and reload the page.
	 *
	 * @static
	 */
	static reload(): void {
		const filter = query<InputElement>('am-filter input');

		if (filter) {
			setSearchParam('filter', filter.value);
		}

		setSearchParam('scroll', String(window.scrollY));

		window.location.reload();
	}

	/**
	 * Restore filter and scrollposition after reloading the page.
	 *
	 * @static
	 */
	static restoreFilterAndScroll(): void {
		const filter = query<InputElement>('am-filter input');
		const savedFilter = getSearchParam('filter');
		const savedScrollY = getSearchParam('scroll');

		if (savedFilter) {
			if (filter) {
				filter.value = savedFilter;

				fire('input', filter);
			}

			deleteSearchParam('filter');
		}

		if (savedScrollY) {
			window.scrollTo(0, parseInt(savedScrollY));

			deleteSearchParam('scroll');
		}
	}

	/**
	 * Set the nav state to not be disabled.
	 *
	 * @static
	 * @param id
	 */
	static removeNavigationLock(id: number): void {
		delete this.navigationLocks[id];
	}

	/**
	 * Get a text module by key.
	 *
	 * @static
	 * @param key
	 * @returns the requested text module
	 */
	static text(key: string): string {
		return App.getState('text')[key] || '';
	}

	/**
	 * Check for system updates.
	 */
	static async checkForSystemUpdate(): Promise<void> {
		const response = await requestAPI(
			'System/checkForUpdate',
			null,
			true,
			null,
			true
		);

		if (!response.data) {
			return;
		}

		State.getInstance().set('systemUpdate', response.data);
		fire(EventName.systemUpdateCheck, window);
	}

	/**
	 * Check for outdated packages.
	 */
	static async checkForOutdatedPackages(): Promise<void> {
		const { data } = await requestAPI(
			'PackageManager/getOutdated',
			null,
			true,
			null,
			true
		);

		State.getInstance().set(
			'outdatedPackages',
			data?.outdated?.length || 0
		);
		fire(EventName.systemUpdateCheck, window);
	}
}
