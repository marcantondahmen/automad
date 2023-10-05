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

import { App, create, createSelect, CSS, debounce, query } from '@/core';
import { CodeBlockData } from '@/types';
import CodeFlask from 'codeflask';
import { BaseBlock } from './BaseBlock';

export const codeBlockLanguages = ['js', 'html', 'css', 'php'] as const;

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
			icon: '<i class="bi bi-code"></i>',
		};
	}

	/**
	 * The CodeFlask instance.
	 */
	private flask: CodeFlask;

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: CodeBlockData): CodeBlockData {
		return { code: data.code ?? '', language: data.language ?? 'html' };
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
			CodeBlock.toolbox.title
		);

		const langSelect = createSelect(
			codeBlockLanguages.map((lang) => ({
				value: lang,
			})),
			this.data.language,
			this.wrapper
		);

		const editor = create(
			'div',
			[CSS.editorBlockCode],
			{},
			this.wrapper
		) as HTMLDivElement;

		this.api.listeners.on(langSelect, 'change', (event: Event) => {
			this.data.language =
				langSelect.value as unknown as (typeof codeBlockLanguages)[number];

			this.initEditor(editor);
		});

		this.initEditor(editor);

		return this.wrapper;
	}

	/**
	 * Create a fresh CodeFlask instance.
	 *
	 * @param editor
	 */
	private initEditor(editor: HTMLDivElement): void {
		editor.innerHTML = '';

		this.flask = new CodeFlask(editor, {
			lineNumbers: false,
			defaultTheme: false,
			handleTabs: true,
			tabSize: 4,
			language: this.data.language,
		});

		this.flask.updateCode(this.data.code);

		const pre = query('pre', editor);

		this.flask.onUpdate(
			debounce(() => {
				editor.style.height = `${pre.getBoundingClientRect().height}px`;
				this.data.code = this.flask.getCode();
			}, 50)
		);

		this.api.listeners.on(
			query('textarea', editor),
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
			code: this.flask.getCode(),
			language: this.data.language,
		};
	}
}
