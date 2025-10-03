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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	API,
	BlockAPI,
	BlockTool,
	BlockToolConstructorOptions,
	BlockToolData,
} from 'automad-editorjs';
import { create, listen } from '@/admin/core';
import { KeyValueMap, Listener } from '@/admin/types';

/**
 * The abstract base block class.
 */
export abstract class BaseBlock<DataType extends object> implements BlockTool {
	/**
	 * Allow to press Enter inside the text field.
	 *
	 * @returns boolean
	 * @static
	 */
	static get enableLineBreaks(): boolean {
		return false;
	}

	/**
	 * Returns true to notify the core that read-only mode is supported
	 *
	 * @returns boolean
	 * @static
	 */
	static get isReadOnlySupported(): boolean {
		return true;
	}

	/**
	 * Make block read-only.
	 */
	readOnly: boolean;

	/**
	 * The editor API.
	 */
	protected api: API;

	/**
	 * The tool's data.
	 */
	protected data: DataType;

	/**
	 * The tool configuration.
	 */
	protected config: KeyValueMap;

	/**
	 * The block API.
	 */
	protected blockAPI: BlockAPI;

	/**
	 * The wrapper element.
	 */
	protected wrapper: HTMLElement;

	/**
	 * Listeners that will be removed on destroy.
	 */
	protected listeners: Listener[] = [];

	/**
	 * The constructor.
	 *
	 * @param options
	 * @param options.data
	 * @param options.api
	 * @param options.config
	 * @param options.block
	 */
	constructor({
		data,
		api,
		config,
		block,
		readOnly,
	}: BlockToolConstructorOptions) {
		this.api = api;
		this.data = this.prepareData(data || ({} as DataType));
		this.config = config;
		this.blockAPI = block;
		this.readOnly = readOnly;
		this.wrapper = create('div', ['cdx-block']);
	}

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: DataType): DataType {
		return data;
	}

	/**
	 * Add listener.
	 *
	 * @param listener
	 */
	protected addListener(listener: Listener): void {
		this.listeners.push(listener);
	}

	/**
	 * Create a listener that will be removed on destruction.
	 *
	 * @param element - the element to register the event listeners to
	 * @param eventNamesString - a string of one or more event names separated by a space
	 * @param callback - the callback
	 * @param [selector] - the sector to be used as filter
	 */
	protected listen(
		element: HTMLElement | Document | Window,
		eventNamesString: string,
		callback: (event: Event) => void,
		selector: string = ''
	): void {
		this.addListener(listen(element, eventNamesString, callback, selector));
	}

	/**
	 * Render the block.
	 *
	 * @return the rendered element
	 */
	abstract render(): HTMLElement;

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	abstract save(): BlockToolData<DataType>;

	/**
	 * Remove listeners.
	 */
	destroy(): void {
		this.listeners.forEach((listener) => {
			listener.remove();
		});
	}
}
