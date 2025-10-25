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

import { create, CSS, debounce, html, query } from '@/admin/core';
import { CodeEditor } from '@/admin/core/code';
import { TeXBlockData } from '@/admin/types';
import { BaseBlock } from './BaseBlock';

export class TeXBlock extends BaseBlock<TeXBlockData> {
	/**
	 * Allow to press Enter inside the Quote
	 *
	 * @return boolean
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
			title: html`
				Math
				<span class="${CSS.displayNone}">latex,katex</span>
			`,
			icon: '<small><strong>∑</strong></small>',
		};
	}

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: TeXBlockData): TeXBlockData {
		return { code: data.code ?? '' };
	}

	/**
	 * Render the block.
	 *
	 * @return the rendered element
	 */
	render(): HTMLElement {
		this.wrapper.classList.add(CSS.flex, CSS.flexColumn, CSS.flexGap);
		this.init();

		return this.wrapper;
	}

	/**
	 * The async init funtion.
	 */
	private async init(): Promise<void> {
		const katex = await import('katex');
		const placeholder = 'x = \\frac{-b \\pm \\sqrt{b^2 - 4ac}}{2a}';

		if (!this.readOnly) {
			create(
				'span',
				[CSS.textMuted, CSS.userSelectNone],
				{},
				this.wrapper,
				html`
					<span class="${CSS.iconText}">
						<strong>∑</strong>
						<span>${TeXBlock.toolbox.title}</span>
					</span>
				`
			);
		}

		let preview: HTMLDivElement;

		const container = create(
			'div',
			[
				CSS.flex,
				CSS.flexColumn,
				CSS.editorBlockTex,
				...(this.readOnly ? [CSS.editorBlockTexReadOnly] : []),
			],
			{},
			this.wrapper
		);

		const renderPreview = debounce(async (): Promise<void> => {
			preview = preview ?? create('figure', [], {}, container);

			container.classList.toggle(
				CSS.editorBlockTexPlaceholder,
				!this.data.code
			);

			katex.render(this.data.code || placeholder, preview, {
				throwOnError: false,
				output: 'html',
				displayMode: true,
				errorColor: 'hsl(var(--am-clr-text-danger))',
			});
		}, 500);

		if (!this.readOnly) {
			const editor = create(
				'div',
				[CSS.codeflask],
				{},
				container
			) as HTMLDivElement;

			new CodeEditor({
				element: editor,
				code: this.data.code,
				language: 'latex',
				onChange: (code) => {
					this.data.code = code;

					renderPreview();
				},
				placeholder,
				readonly: this.readOnly,
			});

			this.api.listeners.on(
				query('textarea', editor),
				'keydown',
				(event: Event) => {
					event.stopImmediatePropagation();
				}
			);
		}

		renderPreview();
	}

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	save(): TeXBlockData {
		return this.data;
	}
}
