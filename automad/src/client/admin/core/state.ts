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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { RootComponent } from '../components/Root';
import { KeyValueMap } from '../types';
import { EventName, fire } from './events';
import { getLogger } from './logger';

export class State {
	/**
	 * The singelton instance.
	 */
	private static instance: State = null;

	/**
	 * Get the singelton instance.
	 *
	 * @static
	 * @returns the singelton instance
	 */
	static getInstance(): State {
		if (!State.instance) {
			State.instance = new State();

			getLogger().log('Created new State instance');
		}

		return State.instance;
	}

	/**
	 * The root component.
	 */
	private _root: RootComponent = null;

	/**
	 * The state data storage.
	 */
	private _data: KeyValueMap = null;

	/**
	 * The app's root component.
	 */
	get root(): RootComponent {
		return this._root;
	}

	/**
	 * The state data.
	 */
	get data(): KeyValueMap {
		return this._data;
	}

	/**
	 * The private constructor.
	 */
	private constructor() {}

	/**
	 * Bootstrap or reset the state instance.
	 *
	 * @param root the app's root component
	 * @param data the initial data
	 */
	bootstrap(root: RootComponent, data: KeyValueMap): void {
		this._root = root;
		this._data = data;
	}

	/**
	 * Update the current state by merging the given data object with the internal one.
	 *
	 * @param data the data to be merged
	 */
	update(data: KeyValueMap): void {
		this._data = Object.assign({}, this._data, data);

		fire(EventName.appStateChange);
	}

	/**
	 * Get a value from the internal state data object.
	 *
	 * @param key the key for the requested item
	 * @return the value for a given key
	 */
	get(key: keyof KeyValueMap): KeyValueMap[keyof KeyValueMap] {
		return this._data[key];
	}

	/**
	 * Set a value in the internal state data object.
	 *
	 * @param key
	 * @param value
	 */
	set(key: keyof KeyValueMap, value: KeyValueMap[keyof KeyValueMap]): void {
		this._data[key] = value;
	}
}
