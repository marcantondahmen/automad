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
import { App, create, CSS, query } from '@/admin/core';
import { ParagraphBlockData } from '@/admin/types';
import { BaseBlock } from './BaseBlock';

export class ParagraphBlock extends BaseBlock<ParagraphBlockData> {
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
			tags: ['P'],
		};
	}

	/**
	 * The sanitizer config.
	 *
	 * @static
	 */
	static get sanitize() {
		return {
			large: false,
			text: {
				br: true,
			},
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
			title: App.text('textTool'),
			icon: '<i class="bi bi-text-paragraph"></i>',
		};
	}

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: ParagraphBlockData): ParagraphBlockData {
		return {
			text: data.text ?? '',
			large: data.large ?? false,
		};
	}

	/**
	 * The content of the paragraph.
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
		this.wrapper.classList.toggle(
			`${CSS.editorStyleBase}--large`,
			this.data.large
		);

		create(
			'div',
			[CSS.editorBlockParagraph],
			{
				contenteditable: this.readOnly ? 'false' : 'true',
				placeholder:
					this.api.blocks.getBlocksCount() === 0
						? this.config.placeholder || ''
						: '',
			},
			this.wrapper
		).innerHTML = this.data.text;

		return this.wrapper;
	}

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	save(): ParagraphBlockData {
		return {
			text: this.content,
			large: this.data.large,
		};
	}

	/**
	 * Create the tunes menu configuration.
	 *
	 * @return the tunes menu configuration
	 */
	renderSettings(): TunesMenuConfig {
		return {
			icon: '<i class="bi bi-capslock"></i>',
			label: App.text('large'),
			closeOnActivate: false,
			onActivate: () => {
				this.data.large = !this.data.large;
				this.wrapper.classList.toggle(
					`${CSS.editorStyleBase}--large`,
					this.data.large
				);
			},
			isActive: this.data.large,
			toggle: 'large',
		};
	}

	/**
	 * The merge settings.
	 *
	 * @param data
	 */
	merge(data: ParagraphBlockData): void {
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
	validate(data: ParagraphBlockData): boolean {
		return data.text.replace(/<br>/, '').trim() !== '';
	}

	/**
	 * Called when content is pasted.
	 *
	 * @param event
	 */
	onPaste(event: HTMLPasteEvent): void {
		const data = {
			text: event.detail.data.innerHTML,
			large: false,
		};

		const div = query('[contenteditable]', this.wrapper);
		div.innerHTML = data.text;
	}
}
