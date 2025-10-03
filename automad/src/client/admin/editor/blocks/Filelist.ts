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
	query,
	uniqueId,
} from '@/admin/core';
import {
	FilelistBlockData,
	KeyValueMap,
	SelectComponentOption,
} from '@/admin/types';
import { BaseBlock } from './BaseBlock';

const defaultFile = 'default';

export class FilelistBlock extends BaseBlock<FilelistBlockData> {
	/**
	 * Sanitizer settings.
	 *
	 * @static
	 */
	static get sanitize() {
		return {
			file: false,
			glob: false,
			sortOrder: false,
		};
	}

	/**
	 * Toolbox settings.
	 */
	static get toolbox() {
		return {
			title: App.text('filelistBlockTitle'),
			icon: '<i class="bi bi-files"></i>',
		};
	}

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: FilelistBlockData): FilelistBlockData {
		return {
			file: data.file || defaultFile,
			glob: data.glob || '*.*',
			sortOrder: data.sortOrder ?? 'asc',
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
					${Attr.text}="${FilelistBlock.toolbox.title}"
				></am-icon-text>
			</span>
			<div class="${CSS.card} __card"></div>
		`;

		this.renderForm(query('.__card', this.wrapper));

		if (!this.readOnly) {
			this.listen(
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
		const files = App.files.filelist.reduce(
			(res: SelectComponentOption[], value) => [
				...res,
				{
					value,
					text: blockTemplateName(value),
				},
			],
			[{ value: defaultFile, text: App.text('filelistBlockDefaultFile') }]
		);

		const form1 = create('div', [CSS.cardForm], {}, container);
		const form2 = create('div', [CSS.cardForm], {}, container);

		const grid = create('div', [CSS.grid, CSS.gridAuto], {}, form2);

		const attr: KeyValueMap = {};

		if (this.readOnly) {
			attr.disabled = '';
		}

		createSelectField(
			App.text('filelistBlockFile'),
			createSelect(
				files,
				this.data.file,
				null,
				'file',
				null,
				'',
				[],
				attr
			),
			form1
		);

		createField(
			FieldTag.input,
			grid,
			{
				key: uniqueId(),
				name: 'glob',
				value: this.data.glob,
				label: `${App.text('filelistBlockPattern')} (Glob)`,
			},
			[],
			attr
		);

		createSelectField(
			App.text('filelistBlockSortOrder'),
			createSelect(
				[
					{ value: 'asc', text: '↑ asc' },
					{ value: 'desc', text: '↓ desc' },
				],
				this.data.sortOrder,
				null,
				'sortOrder',
				null,
				'',
				[],
				attr
			),
			grid
		);
	}

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	save(): FilelistBlockData {
		return collectFieldData(this.wrapper) as FilelistBlockData;
	}
}
