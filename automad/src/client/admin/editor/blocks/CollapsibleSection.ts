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
	create,
	createEditor,
	createField,
	createGenericModal,
	CSS,
	FieldTag,
	getComponentTargetContainer,
	html,
	uniqueId,
} from '@/admin/core';
import { CollapsibleSectionBlockData } from '@/admin/types';
import { API } from 'automad-editorjs';
import { TunesMenuConfig } from 'automad-editorjs/types/tools/index';
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
	 * The group binding.
	 */
	private groupBinding: Binding;

	/**
	 * The group field key and binding name.
	 */
	private groupId: string;

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
			icon: '<i class="bi bi-view-list"></i>',
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
		this.wrapper.classList.add(CSS.editorBlockCollapsibleSection);
		this.groupId = uniqueId();
		this.groupBinding = new Binding(this.groupId, {
			initial: this.data.group,
		});

		const label = create(
			'span',
			[CSS.editorBlockCollapsibleSectionLabel],
			{},
			create('div', [CSS.flex], {}, this.wrapper),
			html` ${CollapsibleSectionBlock.toolbox.icon} `
		);

		const groupName = create(
			'span',
			[CSS.flex, CSS.flexGap, CSS.cursorPointer],
			{
				[Attr.bind]: this.groupId,
				[Attr.tooltip]: App.text('collapsibleTooltipGroup'),
				placeholder: App.text('collapsibleNoGroup'),
			},
			label
		);

		this.listen(groupName, 'click', this.setGroup.bind(this));

		this.details = create(
			'details',
			[],
			{
				[Attr.bind]: this.groupId,
				[Attr.bindTo]: 'name',
				...(this.data.collapsed ? {} : { open: '' }),
			},
			this.wrapper
		);

		if (this.readOnly) {
			create('summary', [], {}, this.details, this.data.title);
		} else {
			const title = create(
				'div',
				[],
				{ contenteditable: 'true' },
				create('summary', [], {}, this.details),
				this.data.title
			);

			this.listen(title, 'input', () => {
				this.data.title = title.textContent;
			});

			this.listen(title, 'click', (event) => {
				event.stopPropagation();
				event.preventDefault();
			});
		}

		const section = create(
			'div',
			[CSS.editorBlockCollapsibleSectionEditor],
			{},
			this.details
		);

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
	 * Create the tunes menu configuration.
	 *
	 * @return the tunes menu configuration
	 */
	renderSettings(): TunesMenuConfig {
		if (!this.groupId) {
			return;
		}

		const label = App.text('collapsibleLabelGroup');

		return {
			icon: '<i class="bi bi-tag"></i>',
			label: this.data.group
				? `${this.data.group}<span class="${CSS.displayNone}">${label}</span>`
				: `<span class="${CSS.textMuted}">${label}</span>`,
			closeOnActivate: true,
			onActivate: this.setGroup.bind(this),
		};
	}

	/**
	 * Show the group name modal window.
	 */
	setGroup(): void {
		const label = App.text('collapsibleLabelGroup');
		const { modal, body } = createGenericModal(label);

		create(
			'div',
			[CSS.textParagraph],
			{},
			body,
			App.text('collapsibleTooltipGroup')
		);

		const field = createField(FieldTag.input, body, {
			key: this.groupId,
			name: 'group',
			value: this.data.group,
			label,
		});

		modal.listen(field.input, 'input', () => {
			this.data.group = field.input.value;
			this.groupBinding.value = field.input.value;
		});

		setTimeout(() => {
			modal.open();
		}, 0);
	}

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	save(): CollapsibleSectionBlockData {
		return {
			...this.data,
			collapsed: !this.details.open,
		};
	}
}
