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
 * Copyright (c) 2023-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { App, Attr, create, CSS, html, query } from '@/admin/core';
import { CodeEditor } from '@/admin/core/code';
import { BaseBlock } from './BaseBlock';

interface RawBlockData {
	code: string;
}

export class RawBlock extends BaseBlock<RawBlockData> {
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
	 *
	 * @static
	 */
	static get sanitize() {
		return {
			code: true,
		};
	}

	/**
	 * Toolbox settings.
	 *
	 * @static
	 */
	static get toolbox() {
		return {
			title: App.text('rawHtmlMarkdown'),
			icon: '<i class="bi bi-markdown"></i>',
		};
	}

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: RawBlockData): RawBlockData {
		return { code: data.code ?? '' };
	}

	/**
	 * Render the block.
	 *
	 * @return the rendered element
	 */
	render(): HTMLElement {
		this.wrapper.classList.add(CSS.flex, CSS.flexColumn, CSS.flexGap);

		create(
			'span',
			[CSS.textMuted, CSS.userSelectNone],
			{},
			this.wrapper,
			html`
				<am-icon-text
					${Attr.icon}="markdown"
					${Attr.text}="${RawBlock.toolbox.title}"
				></am-icon-text>
			`
		);

		const container = create(
			'div',
			[CSS.codeflask],
			{},
			this.wrapper
		) as HTMLDivElement;

		this.initEditor(container);

		return this.wrapper;
	}

	/**
	 * Create a fresh CodeFlask instance.
	 *
	 * @param editor
	 */
	private initEditor(container: HTMLDivElement): void {
		new CodeEditor({
			element: container,
			code: this.data.code,
			language: 'html',
			onChange: (code) => {
				this.data.code = code;
			},
			readonly: this.readOnly,
		});

		if (!this.readOnly) {
			this.api.listeners.on(
				query('textarea', container),
				'keydown',
				(event: Event) => {
					event.stopImmediatePropagation();
				}
			);
		}
	}

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	getData(): RawBlockData {
		return this.data;
	}
}
