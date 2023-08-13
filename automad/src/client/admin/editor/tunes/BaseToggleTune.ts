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
import { BlockTuneConstructorOptions } from '@/types';
import { TunesMenuConfig } from '@editorjs/editorjs/types/tools';
import { query } from '@/core';

/**
 * The abstract base tune class that retuns a config object on render.
 */
export abstract class BaseToggleTune implements BlockTune {
	/**
	 * The editor API.
	 */
	protected api: API;

	/**
	 * The tune state.
	 */
	protected state: boolean;

	/**
	 * The tool configuration.
	 */
	protected config: ToolConfig;

	/**
	 * The block API.
	 */
	protected block: BlockAPI;

	/**
	 * The tune icon.
	 */
	abstract get icon(): string;

	/**
	 * The default title, also used for filtering.
	 */
	abstract get title(): string;

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
		this.state = data || false;
	}

	/**
	 * Save the tune state.
	 *
	 * @return the saved state
	 */
	save(): boolean {
		return this.state;
	}

	/**
	 * Return the actual tune config.
	 *
	 * @return the config object
	 */
	render(): TunesMenuConfig {
		return {
			icon: this.icon,
			label: this.title,
			closeOnActivate: false,
			onActivate: () => {
				this.state = !this.state;
				this.wrap(query(':scope > *', this.block.holder));
				this.block.dispatchChange();
			},
			isActive: this.state,
			toggle: 'large',
		};
	}

	/**
	 * Apply tune to block content element.
	 *
	 * @param the block content element
	 * @return the element
	 */
	abstract wrap(blockElement: HTMLElement): HTMLElement;
}
