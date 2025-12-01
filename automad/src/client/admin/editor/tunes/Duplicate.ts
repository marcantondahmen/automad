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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { API, BlockAPI, BlockTune, TunesMenuConfig } from '@/admin/vendor/editorjs';
import { App } from '@/admin/core';
import { BlockTuneConstructorOptions } from '@/admin/types';
import { insertBlock } from '../utils';

/**
 * Duplicate blocks tune.
 */
export class DuplicateTune implements BlockTune {
	/**
	 * Define tool to be a tune.
	 */
	static get isTune() {
		return true;
	}

	/**
	 * The editor API.
	 */
	private api: API;

	/**
	 * The block API.
	 */
	private block: BlockAPI;

	/**
	 * The tune constructor.
	 *
	 * @param options
	 * @param options.api
	 * @param options.block
	 */
	constructor({ api, block }: BlockTuneConstructorOptions) {
		this.api = api;
		this.block = block;
	}

	/**
	 * Render the config object.
	 *
	 * @returns the tune config
	 */
	render(): TunesMenuConfig {
		return {
			icon: '<i class="bi bi-copy"></i>',
			sort: 290,
			label: App.text('duplicate'),
			closeOnActivate: true,
			onActivate: this.duplicate.bind(this),
		};
	}

	/**
	 * The actual duplication function.
	 */
	private async duplicate(): Promise<void> {
		const targetIndex = this.api.blocks.getCurrentBlockIndex() + 1;

		insertBlock(this.block, this.api, targetIndex);
	}
}
