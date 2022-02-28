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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { RootComponent } from '../components/Root';
import { fire, request, requestAPI } from '.';
import { KeyValueMap, Pages, ThemeCollection } from '../types';

export const appStateChangedEventName = 'AutomadAppStateChange';

/**
 * The static class that provides the app state and root element to be used across the application.
 */
export class App {
	/**
	 * The internal private state.
	 *
	 * @static
	 */
	private static _state: KeyValueMap;

	/**
	 * The internal private root element
	 *
	 * @static
	 */
	private static _root: RootComponent;

	/**
	 * The autocomplete map.
	 *
	 * @static
	 */
	static get autocomplete(): KeyValueMap[] {
		return this._state.autocomplete;
	}

	/**
	 * The array of allowed file types.
	 *
	 * @static
	 */
	static get allowedFileTypes(): string[] {
		return this._state.allowedFileTypes;
	}

	/**
	 * The base URL for the website.
	 *
	 * @static
	 */
	static get baseURL(): string {
		return this._state.base;
	}

	/**
	 * The dashboard URL.
	 *
	 * @static
	 */
	static get dashboardURL(): string {
		return this._state.dashboard;
	}

	/**
	 * The jumpbar autocomplete map.
	 *
	 * @static
	 */
	static get jumpbar(): KeyValueMap[] {
		return this._state.jumpbar;
	}

	/**
	 * The main theme path.
	 *
	 * @static
	 */
	static get mainTheme(): string {
		return this._state.mainTheme;
	}

	/**
	 * The pages array used to build the nav tree.
	 *
	 * @static
	 */
	static get pages(): Pages {
		return this._state.pages;
	}

	/**
	 * The map of reserved field names.
	 *
	 * @static
	 */
	static get reservedFields(): KeyValueMap {
		return this._state.reservedFields;
	}

	/**
	 * The section name map.
	 *
	 * @static
	 */
	static get sections(): KeyValueMap {
		return this._state.sections;
	}

	/**
	 * The name of the site.
	 *
	 * @static
	 */
	static get sitename(): string {
		return this._state.sitename;
	}

	/**
	 * The array of tags that are used across the site.
	 *
	 * @static
	 */
	static get tags(): string[] {
		return this._state.tags;
	}

	/**
	 * The array of installed themes.
	 *
	 * @static
	 */
	static get themes(): ThemeCollection {
		return this._state.themes;
	}

	/**
	 * The state.
	 *
	 * @static
	 */
	static get state(): KeyValueMap {
		return this._state;
	}

	/**
	 * The root element.
	 *
	 * @static
	 */
	static get root(): RootComponent {
		return this._root;
	}

	/**
	 * The bootstrap method that requested the basic state data.
	 *
	 * @static
	 * @async
	 * @param root
	 */
	static async bootstrap(root: RootComponent): Promise<void> {
		this._root = root;

		const api = `${root.elementAttributes.base}/api`;
		const response = await request(`${api}/App/bootstrap`);
		const json = await response.json();

		this._state = json.data;
	}

	/**
	 * Update the state according to a change of view.
	 *
	 * @async
	 * @static
	 */
	static async updateState(): Promise<void> {
		const response = await requestAPI('App/updateState');

		this._state = Object.assign(this._state, response.data);
		fire(appStateChangedEventName);
	}

	/**
	 * Get a text module by key.
	 *
	 * @static
	 * @param key
	 * @returns the requested text module
	 */
	static text(key: string): string {
		return this._state.text[key] || '';
	}
}
