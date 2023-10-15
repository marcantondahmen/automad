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

import { App, Attr, create, createSelect, CSS, html, query } from '@/core';
import { CodeEditor, codeLanguages } from '@/core/code';
import { CodeBlockData } from '@/types';
import { BaseBlock } from './BaseBlock';

export class CodeBlock extends BaseBlock<CodeBlockData> {
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
			title: App.text('code'),
			icon: '<i class="bi bi-code-slash"></i>',
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
	protected prepareData(data: CodeBlockData): CodeBlockData {
		return { code: data.code ?? '', language: data.language ?? 'none' };
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
			html`
				<am-icon-text
					${Attr.icon}="code-slash"
					${Attr.text}="${CodeBlock.toolbox.title}"
				></am-icon-text>
			`
		);

		const langSelect = createSelect(
			codeLanguages.map((lang) => ({
				value: lang,
			})),
			this.data.language,
			this.wrapper
		);

		const container = create(
			'div',
			[CSS.editorBlockCode],
			{},
			this.wrapper
		) as HTMLDivElement;

		this.api.listeners.on(langSelect, 'change', () => {
			this.data.language =
				langSelect.value as unknown as (typeof codeLanguages)[number];

			this.initEditor(container);
		});

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
			this.data.language,
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
	save(): CodeBlockData {
		return {
			code: this.editor.codeFlask.getCode(),
			language: this.data.language,
		};
	}
}
