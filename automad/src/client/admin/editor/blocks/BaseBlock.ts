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

import {
	API,
	BlockAPI,
	BlockTool,
	BlockToolConstructorOptions,
	BlockToolData,
} from '@editorjs/editorjs';
import { create } from '@/core';
import { KeyValueMap, Listener } from '@/types';

/**
 * The abstract base block class.
 */
export abstract class BaseBlock<DataType extends object> implements BlockTool {
	/**
	 * Allow to press Enter inside the text field.
	 *
	 * @static
	 */
	static get enableLineBreaks() {
		return false;
	}

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
	constructor({ data, api, config, block }: BlockToolConstructorOptions) {
		this.api = api;
		this.data = this.prepareData(data || ({} as DataType));
		this.config = config;
		this.blockAPI = block;
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
