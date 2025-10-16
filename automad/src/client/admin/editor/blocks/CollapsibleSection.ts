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

import { EditorJSComponent } from '@/admin/components/EditorJS';
import {
	App,
	Attr,
	Binding,
	collectFieldData,
	create,
	createEditor,
	createField,
	CSS,
	FieldTag,
	uniqueId,
} from '@/admin/core';
import { CollapsibleSectionBlockData } from '@/admin/types';
import { API } from 'automad-editorjs';
import { BaseBlock } from './BaseBlock';

export class CollapsibleSectionBlock extends BaseBlock<CollapsibleSectionBlockData> {
	/**
	 * The editor holder element.
	 */
	private holder: EditorJSComponent = null;

	/**
	 * The details element.
	 */
	private details: HTMLDetailsElement;

	/**
	 * The form element.
	 */
	private form: HTMLDivElement;

	/**
	 * Sanitizer rules
	 */
	static get sanitize() {
		return {
			title: true,
			content: true,
		};
	}

	/**
	 * Enable linebreaks.
	 *
	 * @static
	 */
	static get enableLineBreaks() {
		return true;
	}

	/**
	 * Toolbox settings.
	 */
	static get toolbox() {
		return {
			title: App.text('collapsibleSectionBlockTitle'),
			icon: '<i class="bi bi-caret-down-square"></i>',
		};
	}

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(
		data: CollapsibleSectionBlockData
	): CollapsibleSectionBlockData {
		return {
			title: data.title || '',
			content: data.content || { blocks: [] },
			group: data.group || '',
			collapsed: data.collapsed ?? false,
		};
	}

	/**
	 * Render the block.
	 *
	 * @return the rendered element
	 */
	render(): HTMLElement {
		this.form = create('div', [CSS.grid, CSS.gridAuto], {}, this.wrapper);

		createField(FieldTag.input, this.form, {
			key: uniqueId(),
			name: 'title',
			value: this.data.title,
			label: App.text('collapsibleLabelTitle'),
		});

		const groupFieldId = uniqueId();
		const groupField = createField(FieldTag.input, this.form, {
			key: groupFieldId,
			name: 'group',
			value: this.data.group,
			label: App.text('collapsibleLabelGroup'),
		});

		new Binding(groupFieldId, {
			input: groupField.input,
		});

		this.details = create(
			'details',
			[],
			{
				[Attr.bind]: groupFieldId,
				[Attr.bindTo]: 'name',
				...(this.data.collapsed ? {} : { open: '' }),
			},
			this.wrapper,
			`<summary>${App.text('collapsibleLabelDetails')}</summary>`
		);

		const section = create('div', [], {}, this.details);

		const renderEditor = async () => {
			this.holder = createEditor(
				section,
				this.data.content,
				{
					onChange: async (api: API) => {
						if (this.readOnly) {
							return;
						}

						const { blocks } = await api.saver.save();

						this.data.content = { blocks };
						this.blockAPI.dispatchChange();
					},
				},
				true,
				this.readOnly
			);

			await this.holder.editor.isReady;

			if (!this.readOnly) {
				this.holder.editor.focus();

				this.listen(this.holder, 'paste', (event: Event) => {
					event.stopPropagation();
				});
			}
		};

		renderEditor();

		return this.wrapper;
	}

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	save(): CollapsibleSectionBlockData {
		return {
			...this.data,
			...collectFieldData(this.form),
			collapsed: !this.details.open,
		};
	}
}
