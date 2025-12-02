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

import { ConversionConfig } from '@/vendor/editorjs';
import { App, create, CSS, html } from '@/admin/core';
import { QuoteBlockData, QuoteBlockInputs } from '@/admin/types';
import { BaseBlock } from './BaseBlock';

export class QuoteBlock extends BaseBlock<QuoteBlockData> {
	/**
	 * Allow Quote to be converted to/from other blocks
	 *
	 * @return the conversion config
	 */
	static get conversionConfig(): ConversionConfig {
		return {
			import: 'text',
			export: (quoteData: QuoteBlockData) => {
				return quoteData.caption
					? `${quoteData.text} — ${quoteData.caption}`
					: quoteData.text;
			},
		};
	}

	/**
	 * Allow to press Enter inside the Quote
	 *
	 * @returns boolean
	 * @static
	 */
	static get enableLineBreaks() {
		return true;
	}

	/**
	 * Sanitizer rules
	 */
	static get sanitize() {
		return {
			text: {},
			caption: {},
		};
	}

	/**
	 * Toolbox settings.
	 */
	static get toolbox() {
		return {
			title: App.text('blockquote'),
			icon: '<i class="bi bi-quote"></i>',
		};
	}

	/**
	 * The content fields.
	 */
	private inputs: QuoteBlockInputs;

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: QuoteBlockData): QuoteBlockData {
		return { text: data.text || '', caption: data.caption || '' };
	}

	/**
	 * Render the block.
	 *
	 * @return the rendered element
	 */
	render(): HTMLElement {
		this.wrapper.classList.add(
			CSS.editorBlockQuote,
			CSS.flex,
			CSS.flexColumn
		);

		this.inputs = {
			text: create(
				'div',
				[CSS.editorBlockQuoteText, 'ce-paragraph'],
				{
					contenteditable: this.readOnly ? 'false' : 'true',
					placeholder: App.text('blockquote'),
				},
				this.wrapper,
				html`${this.data.text}`
			),
			caption: create(
				'div',
				[CSS.editorBlockQuoteCaption, 'ce-paragraph'],
				{
					contenteditable: this.readOnly ? 'false' : 'true',
					placeholder: `— ${App.text('caption')}`,
				},
				this.wrapper,
				html`${this.data.caption}`
			),
		};

		return this.wrapper;
	}

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	save(): QuoteBlockData {
		return {
			text: this.inputs.text.innerHTML,
			caption: this.inputs.caption.innerHTML,
		};
	}
}
