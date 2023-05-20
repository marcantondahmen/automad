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
import { KeyValueMap } from '@/types';

export abstract class BaseBlock<DataType extends object> implements BlockTool {
	protected api: API;
	protected data: DataType;
	protected config: KeyValueMap;
	protected blockAPI: BlockAPI;
	protected wrapper: HTMLElement;

	constructor({ data, api, config, block }: BlockToolConstructorOptions) {
		this.api = api;
		this.data = this.prepareData(data || ({} as DataType));
		this.config = config;
		this.blockAPI = block;
		this.wrapper = create('div');
	}

	protected prepareData(data: DataType): DataType {
		return data;
	}

	abstract render(): HTMLElement;

	abstract save(): BlockToolData<DataType>;
}
