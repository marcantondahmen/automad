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

import { ModalComponent } from '@/components/Modal/Modal';
import {
	App,
	Attr,
	collectFieldData,
	create,
	CSS,
	debounce,
	EventName,
	html,
	listen,
	query,
} from '@/core';
import { BlockTuneConstructorOptions } from '@/types';
import { API, BlockAPI, ToolConfig } from '@editorjs/editorjs';
import { TunesMenuConfig } from '@editorjs/editorjs/types/tools';

/**
 * The abstract base modal tune class.
 */
export abstract class BaseModalTune<DataType extends object> {
	/**
	 * The editor API.
	 */
	protected api: API;

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
		this.data = this.prepareData(data || ({} as DataType));
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
	 * Sanitize form data before setting the current state.
	 *
	 * @param data
	 * @return the sanitized data
	 */
	protected sanitize(data: DataType): DataType {
		return data;
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
		};
	}

	/**
	 * Called when the tune button is activated.
	 */
	protected onActivate(): void {
		const blockIndex = this.api.blocks.getCurrentBlockIndex();
		const blockElement = query(':scope > *', this.block.holder);
		const container = query('.am-ui body, body .am-ui');
		const modal = create(
			ModalComponent.TAG_NAME,
			[],
			{ [Attr.destroy]: '' },
			container,
			html`
				<div class="${CSS.modalDialog}">
					<div class="${CSS.modalBody}"></div>
					<div class="${CSS.modalFooter}">
						<am-modal-close
							class="${CSS.button} ${CSS.buttonAccent}"
						>
							${App.text('close')}
						</am-modal-close>
					</div>
				</div>
			`
		) as ModalComponent;

		const body = query(`.${CSS.modalBody}`, modal);

		const onChange = debounce((event: Event) => {
			const data = collectFieldData(modal) as DataType;

			this.data = this.sanitize(data);
		}, 50);

		body.appendChild(this.createForm());

		listen(body, 'input change', onChange.bind(this));

		listen(modal, EventName.modalClose, () => {
			this.wrap(blockElement);
			this.block.dispatchChange();
			this.api.caret.setToBlock(blockIndex, 'start');
			this.api.toolbar.toggleBlockSettings(true);
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
	save(): DataType {
		return this.data;
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
	 * Apply tune to block content element.
	 *
	 * @param the block content element
	 * @return the element
	 */
	protected wrap(blockElement: HTMLElement): HTMLElement {
		return blockElement;
	}
}
