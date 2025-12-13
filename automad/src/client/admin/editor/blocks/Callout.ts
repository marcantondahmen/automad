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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { ConversionConfig } from '@/vendor/editorjs';
import { App, create, CSS, html } from '@/admin/core';
import { CalloutBlockData, CalloutBlockInputs } from '@/admin/types';
import { BaseBlock } from './BaseBlock';

export class CalloutBlock extends BaseBlock<CalloutBlockData> {
	/**
	 * Allow callout block to be converted to/from other blocks
	 *
	 * @return the conversion config
	 */
	static get conversionConfig(): ConversionConfig {
		return {
			import: 'text',
			export: (data: CalloutBlockData) => {
				return data.title ? `${data.title}: ${data.text}` : data.text;
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
			title: {},
			text: true,
		};
	}

	/**
	 * Toolbox settings.
	 */
	static get toolbox() {
		return {
			title: html`
				${App.text('callout')}
				<span class="${CSS.displayNone}">alert,note</span>
			`,
			icon: '<i class="bi bi-lightbulb"></i>',
		};
	}

	/**
	 * The content fields.
	 */
	private inputs: CalloutBlockInputs;

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: CalloutBlockData): CalloutBlockData {
		return { title: data.title || '', text: data.text || '' };
	}

	/**
	 * Render the block.
	 *
	 * @return the rendered element
	 */
	render(): HTMLElement {
		const container = create(
			'div',
			[CSS.editorCallout, CSS.flex, CSS.flexColumn],
			{},
			this.wrapper
		);

		this.inputs = {
			title: create(
				'div',
				[CSS.editorCalloutTitle, 'ce-paragraph'],
				{
					contenteditable: this.readOnly ? 'false' : 'true',
					placeholder: `⚠ ${App.text('calloutTitle')}`,
				},
				container,
				html`${this.data.title}`
			),
			text: create(
				'div',
				[CSS.editorCalloutText, 'ce-paragraph'],
				{
					contenteditable: this.readOnly ? 'false' : 'true',
					placeholder: App.text('calloutText'),
				},
				container,
				html`${this.data.text}`
			),
		};

		return this.wrapper;
	}

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	save(): CalloutBlockData {
		return {
			title: this.inputs.title.innerHTML,
			text: this.inputs.text.innerHTML,
		};
	}
}
