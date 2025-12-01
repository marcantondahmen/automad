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

import { API, BlockAPI, BlockTune, ToolConfig } from '@/admin/vendor/editorjs';
import { BlockTuneConstructorOptions } from '@/admin/types';

/**
 * The abstract base tune class that returns an element on render.
 */
export abstract class BaseElementTune<DataType> implements BlockTune {
	/**
	 * The editor API.
	 */
	protected api: API;

	/**
	 * The sort order for this tune.
	 */
	protected sort: number = 100;

	/**
	 * The tune data.
	 */
	protected data: DataType;

	/**
	 * The tool configuration.
	 */
	protected config: ToolConfig;

	/**
	 * The block API.
	 */
	protected block: BlockAPI;

	/**
	 * The wrapper element.
	 */
	protected wrapper: HTMLElement;

	/**
	 * Define tool to be a tune.
	 */
	static get isTune() {
		return true;
	}

	/**
	 * The tune constructor.
	 *
	 * @param options
	 * @param options.api
	 * @param options.data
	 * @param options.config
	 * @param options.block
	 */
	constructor({ api, data, config, block }: BlockTuneConstructorOptions) {
		this.api = api;
		this.config = config;
		this.block = block;
		this.data = this.prepareData(data);
		this.wrapper = this.renderSettings();
	}

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected abstract prepareData(data: DataType): DataType;

	/**
	 * Render the wrapper content.
	 *
	 * @return the rendered wrapper content
	 */
	abstract renderSettings(): HTMLElement;

	/**
	 * Save the tune data.
	 *
	 * @return the saved data
	 */
	save(): DataType {
		return this.data;
	}

	/**
	 * Return the wrapper.
	 *
	 * @return the main wrapper
	 */
	render(): HTMLElement {
		return this.wrapper;
	}
}
