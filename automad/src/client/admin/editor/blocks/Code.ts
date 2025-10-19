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

import {
	App,
	Attr,
	collectFieldData,
	create,
	createSelect,
	CSS,
	html,
	query,
	uniqueId,
} from '@/admin/core';
import { CodeEditor } from '@/admin/core/code';
import { CodeBlockData } from '@/admin/types';
import { supportedLanguages } from '@/prism/prism';
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
		return {
			code: data.code ?? '',
			language: data.language ?? 'none',
			lineNumbers: data.lineNumbers ?? false,
		};
	}

	/**
	 * Render the block.
	 *
	 * @return the rendered element
	 */
	render(): HTMLElement {
		this.wrapper.classList.add(CSS.flex, CSS.flexColumn, CSS.flexGap);

		if (!this.readOnly) {
			create(
				'span',
				[CSS.textMuted, CSS.userSelectNone],
				{},
				this.wrapper,
				html`
					<am-icon-text
						${Attr.icon}="code-slash"
						${Attr.text}="${CodeBlock.toolbox.title}"
					></am-icon-text>
				`
			);
		}

		const container = create(
			'div',
			[CSS.flex, CSS.flexColumn, CSS.flexGap],
			{},
			this.wrapper
		);

		if (!this.readOnly) {
			const settings = create(
				'div',
				[CSS.grid, CSS.gridAuto, CSS.flexGap],
				{},
				container
			);

			createSelect(
				supportedLanguages.map((lang) => ({
					value: lang,
				})),
				this.data.language,
				settings,
				'language'
			);

			const toggleId = uniqueId();

			create(
				'div',
				[CSS.toggle, CSS.toggleButton],
				{},
				settings,
				html`
					<input
						id="${toggleId}"
						type="checkbox"
						name="lineNumbers"
						value="1"
						${this.data.lineNumbers ? 'checked' : ''}
					/>
					<label for="${toggleId}">
						<i class="bi"></i>
						<span>${App.text('codeBlockLineNumbers')}</span>
					</label>
				`
			);

			this.listen(settings, 'change', () => {
				const { language, lineNumbers } = collectFieldData(settings);

				this.data.lineNumbers = lineNumbers;
				this.data.language =
					language as unknown as (typeof supportedLanguages)[number];

				this.initEditor(code);
			});
		}

		const code = create(
			'div',
			[CSS.codeflask],
			{},
			container
		) as HTMLDivElement;

		this.initEditor(code);

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
			language: this.data.language,
			onChange: (code) => {
				this.data.code = code;
			},
			readonly: this.readOnly,
		});

		this.listen(query('textarea', container), 'keydown', (event: Event) => {
			event.stopImmediatePropagation();
		});
	}

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	save(): CodeBlockData {
		return this.data;
	}
}
