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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	blockTemplateName,
	collectFieldData,
	create,
	createField,
	createSelect,
	createSelectField,
	CSS,
	debounce,
	FieldTag,
	html,
	listen,
	query,
	uniqueId,
} from '@/admin/core';
import { SelectComponentOption, SnippetBlockData } from '@/admin/types';
import { BaseBlock } from './BaseBlock';

export class SnippetBlock extends BaseBlock<SnippetBlockData> {
	/**
	 * Sanitizer settings.
	 *
	 * @static
	 */
	static get sanitize() {
		return {
			file: false,
			snippet: true,
		};
	}

	/**
	 * Toolbox settings.
	 */
	static get toolbox() {
		return {
			title: App.text('snippetBlockTitle'),
			icon: '<i class="bi bi-code-square"></i>',
		};
	}

	/**
	 * Allow to press Enter inside the text field.
	 *
	 * @static
	 */
	static get enableLineBreaks() {
		return true;
	}

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: SnippetBlockData): SnippetBlockData {
		return {
			file: data.file || '',
			snippet: data.snippet || '',
		};
	}

	/**
	 * Render the block.
	 *
	 * @return the rendered element
	 */
	render(): HTMLElement {
		this.wrapper.classList.add(CSS.flex, CSS.flexColumn, CSS.flexGap);
		this.wrapper.innerHTML = html`
			<span class="${CSS.textMuted} ${CSS.userSelectNone}">
				<am-icon-text
					${Attr.icon}="files"
					${Attr.text}="${SnippetBlock.toolbox.title}"
				></am-icon-text>
			</span>
			<div class="${CSS.card} __card"></div>
		`;

		this.renderForm(query('.__card', this.wrapper));

		if (!this.readOnly) {
			listen(
				this.wrapper,
				'change input',
				debounce(() => {
					this.blockAPI.dispatchChange();
				}, 50)
			);
		}

		return this.wrapper;
	}

	/**
	 * Render the settings form.
	 *
	 * @param container
	 */
	private renderForm(container: HTMLElement): void {
		const disabled = this.readOnly ? { disabled: '' } : {};
		const files = App.files.snippets.reduce(
			(res: SelectComponentOption[], value) => [
				...res,
				{
					value,
					text: blockTemplateName(value),
				},
			],
			[{ value: '', text: '—' }]
		);

		const form1 = create('div', [CSS.cardForm], {}, container);
		const form2 = create('div', [CSS.cardForm], {}, container);

		createField(
			FieldTag.textarea,
			form1,
			{
				key: uniqueId(),
				name: 'snippet',
				value: this.data.snippet,
				label: App.text('snippetBlockSnippet'),
			},
			[],
			disabled
		);

		createSelectField(
			App.text('snippetBlockFile'),
			createSelect(
				files,
				this.data.file,
				null,
				'file',
				null,
				'',
				[],
				disabled
			),
			form2
		);
	}

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	save(): SnippetBlockData {
		return collectFieldData(this.wrapper) as SnippetBlockData;
	}
}
