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
import { request, requestAPI } from '.';
import { KeyValueMap, ThemeCollection } from '../types';

/**
 * The static class that provides the app state and root element to be used across the application.
 */
export class App {
	/**
	 * The internal private state.
	 */
	private static _state: KeyValueMap;

	/**
	 * The internal private root element
	 */
	private static _root: RootComponent;

	/**
	 * The autocomplete map.
	 */
	static get autocomplete(): KeyValueMap[] {
		return this._state.autocomplete;
	}

	/**
	 * The base URL for the website.
	 */
	static get baseURL(): string {
		return this._state.base;
	}

	/**
	 * The dashboard URL.
	 */
	static get dashboardURL(): string {
		return this._state.dashboard;
	}

	/**
	 * The jumpbar autocomplete map.
	 */
	static get jumpbar(): KeyValueMap[] {
		return this._state.jumpbar;
	}

	/**
	 * The pages array used to build the nav tree.
	 */
	static get pages(): KeyValueMap[] {
		return this._state.pages;
	}

	/**
	 * The map of reserved field names.
	 */
	static get reservedFields(): KeyValueMap {
		return this._state.reservedFields;
	}

	/**
	 * The section name map.
	 */
	static get sections(): KeyValueMap {
		return this._state.sections;
	}

	/**
	 * The name of the site.
	 */
	static get sitename(): string {
		return this._state.sitename;
	}

	/**
	 * The array of tags that are used across the site.
	 */
	static get tags(): string[] {
		return this._state.tags;
	}

	/**
	 * The array of installed themes.
	 */
	static get themes(): ThemeCollection {
		return this._state.themes;
	}

	/**
	 * The state.
	 */
	static get state(): KeyValueMap {
		return this._state;
	}

	/**
	 * The root element.
	 */
	static get root(): RootComponent {
		return this._root;
	}

	/**
	 * The bootstrap method that requested the basic state data.
	 *
	 * @param root
	 */
	static async bootstrap(root: RootComponent): Promise<void> {
		const api = `${root.elementAttributes.base}/api`;
		const response = await request(`${api}/App/bootstrap`);
		const json = await response.json();

		this._root = root;
		this._state = json.data;
	}

	/**
	 * Update the state according to a change of view.
	 */
	static async updateState(): Promise<void> {
		const response = await requestAPI('App/updateState');

		this._state = Object.assign(this._state, response.data);
	}

	/**
	 * Get a text module by key.
	 *
	 * @param key
	 * @returns the requested text module
	 */
	static text(key: string): string {
		return this._state.text[key] || '';
	}
}
