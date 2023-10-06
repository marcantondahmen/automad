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

import { App, create, CSS, query } from '@/core';
import { CodeEditor } from '@/core/code';
import { RawBlockData } from '@/types';
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
		this.wrapper.classList.add(
			'cdx-block',
			CSS.flex,
			CSS.flexColumn,
			CSS.flexGap
		);

		create(
			'span',
			[CSS.textMuted],
			{},
			this.wrapper,
			RawBlock.toolbox.title
		);

		const container = create(
			'div',
			[CSS.editorBlockCode],
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
		this.editor = new CodeEditor(
			container,
			this.data.code,
			'html',
			(code) => {
				this.data.code = code;
			}
		);

		this.api.listeners.on(
			query('textarea', container),
			'keydown',
			(event: Event) => {
				event.stopImmediatePropagation();
			}
		);
	}

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	save(): RawBlockData {
		return {
			code: this.editor.codeFlask.getCode(),
		};
	}
}
