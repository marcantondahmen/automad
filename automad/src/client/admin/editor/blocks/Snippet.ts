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
 * Copyright (c) 2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
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
} from '@/core';
import { SelectComponentOption, SnippetBlockData } from '@/types';
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

		listen(
			this.wrapper,
			'change input',
			debounce(() => {
				this.blockAPI.dispatchChange();
			}, 50)
		);

		return this.wrapper;
	}

	/**
	 * Render the settings form.
	 *
	 * @param container
	 */
	private renderForm(container: HTMLElement): void {
		const files = App.files.snippets.reduce(
			(res: SelectComponentOption[], value) => [
				...res,
				{ value, text: value },
			],
			[{ value: '', text: '—' }]
		);

		const form1 = create('div', [CSS.cardForm], {}, container);
		const form2 = create('div', [CSS.cardForm], {}, container);

		createField(FieldTag.textarea, form1, {
			key: uniqueId(),
			name: 'snippet',
			value: this.data.snippet,
			label: App.text('snippetBlockSnippet'),
		});

		createSelectField(
			App.text('snippetBlockFile'),
			createSelect(files, this.data.file, null, 'file'),
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
