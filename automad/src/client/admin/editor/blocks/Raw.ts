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

import { App, Attr, create, CSS, html, query } from '@/admin/core';
import { CodeEditor } from '@/admin/core/code';
import { RawBlockData } from '@/admin/types';
import { BaseBlock } from './BaseBlock';

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
	 * The CodeFlask instance.
	 */
	private editor: CodeEditor;

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
		this.editor = new CodeEditor({
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
	save(): RawBlockData {
		return this.data;
	}
}
