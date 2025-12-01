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
 * https://automad.org/license
 */

import { HTMLPasteEvent, TunesMenuConfig } from '@/admin/vendor/editorjs';
import { App, create, CSS, html, query } from '@/admin/core';
import { HeaderBlockData } from '@/admin/types';
import { BaseBlock } from './BaseBlock';

export class HeaderBlock extends BaseBlock<HeaderBlockData> {
	/**
	 * The conversion configuration.
	 *
	 * @static
	 */
	static get conversionConfig() {
		return {
			export: 'text',
			import: 'text',
		};
	}

	/**
	 * The paste configuration.
	 *
	 * @static
	 */
	static get pasteConfig() {
		return {
			tags: ['H1', 'H2', 'H3', 'H4', 'H5', 'H6'],
		};
	}

	/**
	 * The sanitizer config.
	 *
	 * @static
	 */
	static get sanitize() {
		return {
			level: false,
			text: { br: true },
		};
	}

	/**
	 * Allow to press Enter inside the text field.
	 *
	 * @static
	 */
	static get enableLineBreaks() {
		return false;
	}

	/**
	 * Toolbox settings.
	 *
	 * @static
	 */
	static get toolbox() {
		return {
			title: App.text('heading'),
			icon: '<i class="bi bi-type-h1"></i>',
		};
	}

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: HeaderBlockData): HeaderBlockData {
		return {
			text: data.text ?? '',
			level: data.level ?? 2,
		};
	}

	/**
	 * The content of the header.
	 *
	 * @return the innerHTML
	 */
	private get content(): string {
		return query('[contenteditable]', this.wrapper)?.innerHTML ?? '';
	}

	/**
	 * Render the block.
	 *
	 * @return the rendered element
	 */
	render(): HTMLElement {
		this.wrapper.innerHTML = '';

		create(
			'div',
			[CSS.editorBlockHeader],
			{ contenteditable: this.readOnly ? 'false' : 'true' },
			create(`h${this.data.level}`, [], {}, this.wrapper)
		).innerHTML = this.data.text;

		return this.wrapper;
	}

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	save(): HeaderBlockData {
		return {
			text: this.content,
			level: this.data.level,
		};
	}

	/**
	 * Create the tunes menu configuration.
	 *
	 * @return the tunes menu configuration
	 */
	renderSettings(): TunesMenuConfig {
		return [1, 2, 3, 4, 5, 6].map((level: HeaderBlockData['level']) => ({
			icon: html`<i class="bi bi-type-h${level}"></i>`,
			label: `${App.text('heading')} ${level}`,
			closeOnActivate: true,
			onActivate: () => {
				this.data.level = level;
				this.data.text = this.content;
				this.render();
			},
			toggle: 'level',
			isActive: this.data.level == level,
		}));
	}

	/**
	 * The merge settings.
	 *
	 * @param data
	 */
	merge(data: HeaderBlockData): void {
		const div = query('[contenteditable]', this.wrapper);
		const index = this.api.blocks.getBlockIndex(this.blockAPI.id);

		div.innerHTML = `${div.innerHTML}${data.text}`;

		setTimeout(() => {
			this.api.caret.setToBlock(index, 'end');
		}, 100);
	}

	/**
	 * Validate the content.
	 *
	 * @param data
	 * @return true if text is not empty
	 */
	validate(data: HeaderBlockData): boolean {
		return data.text.trim() !== '';
	}

	/**
	 * Called when content is pasted.
	 *
	 * @param event
	 */
	onPaste(event: HTMLPasteEvent): void {
		const content = event.detail.data;

		this.data = {
			level:
				(parseInt(
					content.tagName.replace('H', '')
				) as HeaderBlockData['level']) ?? 2,
			text: content.textContent,
		};

		this.render();
	}
}
