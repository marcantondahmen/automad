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

import { API, BlockAPI, BlockTune, ToolConfig } from '@editorjs/editorjs';
import { BlockTuneConstructorOptions } from '../../types';

export abstract class BaseTune<DataType extends object> implements BlockTune {
	protected api: API;
	protected data: DataType;
	protected config: ToolConfig;
	protected block: BlockAPI;
	protected wrapper: HTMLElement;

	static get isTune() {
		return true;
	}

	constructor({ api, data, config, block }: BlockTuneConstructorOptions) {
		this.api = api;
		this.config = config;
		this.block = block;
		this.data = this.prepareData(data || ({} as DataType));
		this.wrapper = this.renderSettings();
	}

	protected prepareData(data: DataType): DataType {
		return data;
	}

	abstract renderSettings(): HTMLElement;

	save(): DataType {
		return this.data;
	}

	render(): HTMLElement {
		return this.wrapper;
	}
}
