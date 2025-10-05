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

import { createGenericModal, CSS, fire, query } from '@/admin/core';
import { BlockTuneConstructorOptions } from '@/admin/types';
import { API, BlockAPI, ToolConfig } from 'automad-editorjs';
import { TunesMenuConfig } from 'automad-editorjs/types/tools';
import { filterEmptyData } from '../utils';

/**
 * The abstract base modal tune class.
 */
export abstract class BaseModalTune<DataType> {
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
	private _data: DataType;

	/**
	 * Set tune data.
	 */
	protected set data(value: DataType) {
		const blockElement = query(':scope > *', this.block.holder);

		this._data = value;
		this.wrap(blockElement);
		this.block.dispatchChange();

		fire('change', blockElement);
	}

	/**
	 * Get tune data.
	 */
	protected get data(): DataType {
		return this._data;
	}

	/**
	 * The tool configuration.
	 */
	protected config: ToolConfig;

	/**
	 * The block API.
	 */
	protected block: BlockAPI;

	/**
	 * Define tool to be a tune.
	 */
	static get isTune() {
		return true;
	}

	/**
	 * The tune icon.
	 */
	abstract get icon(): string;

	/**
	 * The default title, also used for filtering.
	 */
	abstract get title(): string;

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
		this._data = this.prepareData(data ?? null);
	}

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected abstract prepareData(data: Partial<DataType>): DataType;

	/**
	 * Sanitize form data before setting the current state.
	 *
	 * @param data
	 * @return the sanitized data
	 */
	protected sanitize(data: DataType): DataType {
		return data;
	}

	/**
	 * Filter object data before saving it into JSON files in order to save space.
	 *
	 * @param data
	 * @return the filtered data
	 */
	protected filterBeforeSave(data: DataType): Partial<DataType> {
		if (typeof data !== 'object') {
			return data;
		}

		const filtered = filterEmptyData<DataType>(data);

		if (Object.keys(filtered).length === 0) {
			return null;
		}

		return filtered;
	}

	/**
	 * Return the actual tune config.
	 *
	 * @return the config object
	 */
	render(): TunesMenuConfig {
		const label = this.renderLabel();

		return {
			icon: this.icon,
			label: label
				? `${label}<span class="${CSS.displayNone}">${this.title}</span>`
				: `<span class="${CSS.textMuted}">${this.title}</span>`,
			closeOnActivate: true,
			onActivate: this.onActivate.bind(this),
			sort: this.sort,
		};
	}

	/**
	 * Called when the tune button is activated.
	 */
	protected onActivate(): void {
		const { modal, body } = createGenericModal(this.title);

		body.appendChild(this.createForm());

		this.api.listeners.on(body, 'change', () => {
			this.data = this.sanitize(this.getFormData(body));
		});

		setTimeout(() => {
			modal.open();
		}, 0);
	}

	/**
	 * Save the tune data.
	 *
	 * @return the data object
	 */
	save(): Partial<DataType> {
		return this.filterBeforeSave(this.data);
	}

	/**
	 * Render the label.
	 *
	 * @return the rendered label
	 */
	protected abstract renderLabel(): string;

	/**
	 * Create the form fields inside of the modal.
	 *
	 * @return the fields wrapper
	 */
	protected abstract createForm(): HTMLElement;

	/**
	 * Extract field data from the modal.
	 *
	 * @param modal
	 * @return the extracted data
	 */
	protected abstract getFormData(modal: HTMLElement): DataType;

	/**
	 * Apply tune to block content element.
	 *
	 * @param the block content element
	 * @return the element
	 */
	protected wrap(blockElement: HTMLElement): HTMLElement {
		return blockElement;
	}
}
