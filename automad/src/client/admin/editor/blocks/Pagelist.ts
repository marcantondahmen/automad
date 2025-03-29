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
	EventName,
	FieldTag,
	fire,
	html,
	listen,
	query,
	uniqueId,
} from '@/admin/core';
import { PagelistBlockData, SelectComponentOption } from '@/admin/types';
import { BaseBlock } from './BaseBlock';

export const pagelistTypes = [
	'all',
	'children',
	'related',
	'siblings',
] as const;

const defaultFile = 'default';

export class PagelistBlock extends BaseBlock<PagelistBlockData> {
	/**
	 * Sanitizer settings.
	 *
	 * @static
	 */
	static get sanitize() {
		return {
			file: false,
			filter: false,
			matchUrl: false,
			template: false,
			sortField: false,
			sortOrder: false,
			type: false,
		};
	}

	/**
	 * Toolbox settings.
	 */
	static get toolbox() {
		return {
			title: App.text('pagelistBlockTitle'),
			icon: '<i class="bi bi-columns-gap"></i>',
		};
	}

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: PagelistBlockData): PagelistBlockData {
		return {
			type: data.type || pagelistTypes[0],
			context: data.context ?? '',
			excludeHidden: data.excludeHidden ?? true,
			excludeCurrent: data.excludeCurrent ?? false,
			matchUrl: data.matchUrl || '',
			filter: data.filter || '',
			template: data.template || '',
			limit: data.limit ?? 10,
			offset: data.offset || 0,
			sortField: data.sortField ?? ':index',
			sortOrder: data.sortOrder ?? 'asc',
			file: data.file || defaultFile,
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
					${Attr.icon}="columns-gap"
					${Attr.text}="${PagelistBlock.toolbox.title}"
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
		const disabledAttr = this.readOnly ? 'disabled=""' : '';
		const files = App.files.pagelist.reduce(
			(res: SelectComponentOption[], value) => [
				...res,
				{
					value,
					text: blockTemplateName(value),
				},
			],
			[{ value: defaultFile, text: App.text('pagelistBlockDefaultFile') }]
		);

		const types = pagelistTypes.reduce(
			(res: SelectComponentOption[], value) => [...res, { value }],
			[]
		);

		const form1 = create('div', [CSS.cardForm], {}, container);
		const form2 = create('div', [CSS.cardForm], {}, container);

		createSelectField(
			App.text('pagelistBlockFile'),
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
			form1
		);

		const grid1 = create('div', [CSS.grid, CSS.gridAuto], {}, form2);
		const grid2 = create('div', [CSS.grid, CSS.gridAuto], {}, form2);
		const grid3 = create('div', [CSS.grid, CSS.gridAuto], {}, form2);

		const toggle = (name: keyof typeof this.data, label: string) => {
			const id = uniqueId();

			create(
				'div',
				[CSS.toggle, CSS.toggleButton],
				{},
				grid1,
				html`
					<input
						id="${id}"
						type="checkbox"
						name="${name}"
						value="1"
						${disabledAttr}
						${this.data[name] ? 'checked' : ''}
					/>
					<label for="${id}">
						<i class="bi"></i>
						<span>${label}</span>
					</label>
				`
			);
		};

		toggle('excludeHidden', App.text('pagelistBlockExcludeHidden'));
		toggle('excludeCurrent', App.text('pagelistBlockExcludeCurrent'));

		create(
			'div',
			[CSS.field],
			{},
			grid2,
			html`
				<div>
					<label class="${CSS.fieldLabel}">
						${App.text('pagelistBlockSortField')}
					</label>
				</div>
				<am-autocomplete
					name="sortField"
					value="${this.data.sortField}"
					${Attr.data}=":index, :path, date, title"
					${Attr.min}="0"
					${disabledAttr}
				></am-autocomplete>
			`
		);

		createSelectField(
			App.text('pagelistBlockSortOrder'),
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
				disabled
			),
			grid2
		);

		createSelectField(
			App.text('pagelistBlockType'),
			createSelect(
				types,
				this.data.type,
				null,
				'type',
				null,
				'',
				[],
				disabled
			),
			grid2
		);

		createField(
			FieldTag.url,
			grid2,
			{
				key: uniqueId(),
				name: 'context',
				value: this.data.context,
				label: App.text('pagelistBlockContext'),
			},
			[],
			disabled
		);

		this.renderTagSelectField(grid2);

		create(
			'div',
			[CSS.field],
			{},
			grid2,
			html`
				<div>
					<label class="${CSS.fieldLabel}">
						${App.text('pagelistBlockOffset')} /
						${App.text('pagelistBlockLimit')}
					</label>
				</div>
				<div class="${CSS.formGroup}">
					<input
						type="number"
						class="${CSS.input} ${CSS.formGroupItem}"
						name="offset"
						value="${this.data.offset}"
						${disabledAttr}
					/>
					<input
						type="number"
						class="${CSS.input} ${CSS.formGroupItem}"
						name="limit"
						value="${this.data.limit}"
						${disabledAttr}
					/>
				</div>
			`
		);

		createField(
			FieldTag.input,
			grid3,
			{
				key: uniqueId(),
				name: 'matchUrl',
				value: this.data.matchUrl,
				label: `${App.text('pagelistBlockFilterByUrl')} (Regex)`,
			},
			[],
			disabled
		);

		createField(
			FieldTag.input,
			grid3,
			{
				key: uniqueId(),
				name: 'template',
				value: this.data.template,
				label: `${App.text('pagelistBlockFilterByTemplate')} (Regex)`,
			},
			[],
			disabled
		);

		if (!this.readOnly) {
			setTimeout(() => {
				query(':focus', this.wrapper)?.blur();
				fire('click', this.wrapper);
			}, 50);
		}
	}

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	save(): PagelistBlockData {
		const data = collectFieldData(this.wrapper) as PagelistBlockData;

		return {
			excludeCurrent: false,
			excludeHidden: false,
			...data,
		};
	}

	/**
	 * Render the tag select field.
	 *
	 * @param grid
	 */
	private renderTagSelectField(grid: HTMLElement): void {
		const getTags = () => {
			return App.tags.reduce(
				(res: SelectComponentOption[], value) => [...res, { value }],
				[{ value: '', text: '—' }]
			);
		};

		let tags = getTags();

		const { select } = createSelectField(
			App.text('pagelistBlockFilter'),
			createSelect(
				tags,
				this.data.filter,
				null,
				'filter',
				null,
				'',
				[],
				this.readOnly ? { disabled: '' } : {}
			),
			grid
		);

		this.addListener(
			listen(window, EventName.appStateChange, () => {
				const value = select.value;

				select.options = getTags();

				if (App.tags.includes(value)) {
					select.value = value;
				}
			})
		);
	}
}
